<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Normalizer;

use PHPStan\Command\AnalysisResult;

use ArrayObject;
use function array_unique;
use function sprintf;

/**
 * @extends AbstractNormalizer<AnalysisResult>
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class PhpStanNormalizer extends AbstractNormalizer
{
    public const URI_PATTERN = 'https://phpstan.org/error-identifiers/%s';

    public function normalize($data, string $format, array $context): ?ArrayObject
    {
        if (!$this->isSupportedFormat($format)) {
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

        $analysisResult = $data;

        foreach ($analysisResult->getFileSpecificErrors() as $fileSpecificError) {
            $file = $fileSpecificError->getFile();
            $files[] = $file;

            $ruleId = sprintf('%s/%s', $context['rulePrefix'], $fileSpecificError->getIdentifier());
            $helpUri = sprintf(self::URI_PATTERN, $fileSpecificError->getIdentifier());

            $rules[$ruleId] = [
                'name' => $fileSpecificError->getIdentifier(),
                'help' => 'https://phpstan.org/user-guide/ignoring-errors',
                'helpUri' => $helpUri,
            ];

            $attributes = [
                'ReportingDescriptor.id' => $ruleId,
                'Result.fingerprint' => $fileSpecificError->getIdentifier(),
                'Result.message' => $fileSpecificError->getMessage(),
                'Region.startLine' => $fileSpecificError->getLine(),
                'Result.properties.ignorable' => $fileSpecificError->canBeIgnored(),
            ];

            $errors[$file][] = $attributes;
        }

        return [
            'files' => array_unique($files),
            'errors' => $errors,
            'rules' => $rules,
        ];
    }
}
