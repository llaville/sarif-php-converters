<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Normalizer;

use Rector\Contract\Rector\ConfigurableRectorInterface;
use Rector\Contract\Rector\RectorInterface;
use Rector\ValueObject\ProcessResult;

use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use ArrayObject;
use ReflectionClass;
use ReflectionException;
use function array_unique;
use function count;
use function explode;
use function in_array;
use function is_a;
use function sprintf;
use function strtolower;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class RectorNormalizer extends AbstractNormalizer
{
    public const URI_PATTERN = 'https://github.com/rectorphp/rector/blob/main/docs/rector_rules_overview.md#%s';

    public function normalize($data, string $format, array $context): ?ArrayObject
    {
        if (!in_array($format, $this->getSupportedFormats())) {
            return null;
        }

        if (!$data instanceof ProcessResult) {
            return null;
        }

        // internal format (legacy)
        return new ArrayObject($this->fromInternal($data, $context));
    }

    /**
     * @inheritDoc
     */
    protected function fromInternal($data, array $context, array $mapping = []): array
    {
        $files = [];
        $errors = [];
        $rules = [];

        $processResult = $data;

        $fileDiffs = $processResult->getFileDiffs();

        foreach ($fileDiffs as $fileDiff) {
            $filePath = $fileDiff->getAbsoluteFilePath();
            $files[] = $filePath;

            foreach ($fileDiff->getRectorClasses() as $rectorClass) {
                // e.g: Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodParameterRector
                $nameParts = explode('\\', $rectorClass);

                $standard = $nameParts[1];  // e.g: DeadCode
                $group = $nameParts[3];     // e.g: ClassMethod
                $rectorShortClass = $nameParts[count($nameParts) - 1];  // e.g: RemoveUnusedPrivateMethodParameterRector
                $helpUri = sprintf(self::URI_PATTERN, strtolower($rectorShortClass));

                $ruleId = sprintf('%s/%s/%s', $context['rulePrefix'], $standard, $group);

                $rules[$ruleId] = [
                    'name' => $rectorShortClass,
                    'helpUri' => $helpUri,
                ];

                $ruleDefinition = $this->resolveFromClassName($rectorClass);
                if (null !== $ruleDefinition) {
                    $rules[$ruleId]['shortDescription'] = $ruleDefinition->getDescription();
                }
                $isRuleConfigurable = is_a($rectorClass, ConfigurableRectorInterface::class, true);
                $rules[$ruleId]['properties']['configurable'] = $isRuleConfigurable;

                $attributes = [
                    'ReportingDescriptor.id' => $ruleId,
                    'Result.message' => sprintf('Applied rule %s', $rectorShortClass),
                ];
                $errors[$filePath][] = $attributes;
            };
        }

        $systemErrors = $processResult->getSystemErrors();
        foreach ($systemErrors as $error) {
            $filePath = $error->getAbsoluteFilePath();
            $files[] = $filePath;

            $nameParts = explode('\\', \get_class($error));
            $ruleId = sprintf('%s/%s', $context['rulePrefix'], end($nameParts));

            $rules[$ruleId] = [
                'name' => $error->getRectorClass(),
            ];

            $attributes = [
                'ReportingDescriptor.id' => $ruleId,
                'Result.message' => $error->getMessage(),
                'Region.startLine' => $error->getLine() ?? null,
            ];
            $errors[$filePath][] = $attributes;
        }

        return [
            'files' => array_unique($files),
            'errors' => $errors,
            'rules' => $rules,
        ];
    }

    private function resolveFromClassName(string $className): ?RuleDefinition
    {
        try {
            $reflectionClass = new ReflectionClass($className);
            $documentedRule = $reflectionClass->newInstanceWithoutConstructor();
        } catch (ReflectionException $e) {
            return null;
        }

        if ($documentedRule instanceof RectorInterface) {
            return $documentedRule->getRuleDefinition();
        }

        return null;
    }
}
