<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Normalizer;

use Bartlett\Sarif\Contract\NormalizerInterface;

use PHPMD\ProcessingError;
use PHPMD\Report;
use PHPMD\RuleViolation;

use ArrayObject;
use function array_map;
use function array_unique;
use function count;
use function implode;
use function in_array;
use function preg_match;
use function sprintf;
use function str_replace;
use function strtolower;
use function trim;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class PhpMdNormalizer implements NormalizerInterface
{
    public function getSupportedFormats(): array
    {
        return [
            NormalizerInterface::FORMAT_CHECKSTYLE,
            NormalizerInterface::FORMAT_INTERNAL,
        ];
    }

    public function normalize($data, string $format, array $context): ?ArrayObject
    {
        if (!in_array($format, $this->getSupportedFormats())) {
            return null;
        }

        if (NormalizerInterface::FORMAT_CHECKSTYLE === $format) {
            // specialized version of Checkstyle with non-standard attributes
            $innerNormalizer = new SimpleXmlNormalizer();
            $collected = $innerNormalizer->normalize($data, $format, $context);
            return new ArrayObject($this->fromCheckstyle($collected));
        }

        if (!$data instanceof Report) {
            return null;
        }

        // internal format (legacy)
        return new ArrayObject($this->fromInternal($data));
    }

    /**
     * @param ArrayObject<string, mixed>|null $data Data to normalize
     * @return array{
     *     files: string[],
     *     errors: array<string, array<int, array<string, string>>>,
     *     rules: array<string, mixed>
     * }
     */
    private function fromCheckstyle(?ArrayObject $data): array
    {
        $mapping = [
            'line' => 'Region.startLine',
            'endline' => 'Region.endLine',
            'severity' => 'Result.level',
            'message' => 'Result.message',
            'externalInfoUrl' => 'ReportingDescriptor.helpUri',
        ];

        $filenames = [];
        $errors = [];
        $rules = [];

        foreach ($data['file'] as $file) {
            $filename = $file['@attributes']['name'];
            $filenames[] = $filename;

            foreach ($file['error'] as $error) {
                $attrs = count($error) > 1 ? $error : $error['@attributes'];
                $attributes = [];
                foreach ($attrs as $attribute => $value) {
                    $key = $mapping[$attribute] ?? '.' . $attribute;
                    if ('Result.level' === $key) {
                        $attributes[$key] = strtolower($value);
                        if ('info' === $attributes[$key]) {
                            // @link src/Property/Level.php
                            $attributes[$key] = 'note';
                        }
                    } else {
                        $attributes[$key] = $value;
                    }
                }

                // e.g:  (BooleanArgumentFlag, Clean Code Rules)
                if (preg_match('/\(([a-zA-Z]*), ([a-zA-Z ]*)\)/', $attributes['Result.message'], $matches) !== 1) {
                    // invalid "{messageString} ({ruleName}, {rulesetName}) " format
                    $ruleId = null;
                    $ruleDef = [];
                } else {
                    $ruleName = $matches[1];
                    $ruleSet = str_replace(' ', '', $matches[2]);
                    $ruleId = sprintf('%s/%s', $ruleSet, $ruleName);
                    $ruleDef = [
                        'name' => $ruleName,
                        'shortDescription' => sprintf('%s: %s', $ruleSet, $ruleName),
                        'helpUri' => $attributes['ReportingDescriptor.helpUri'],
                        'properties' => ['ruleSet' => $matches[2]],
                    ];
                }

                if (!empty($ruleId)) {
                    $rules[$ruleId] = $ruleDef;
                    $attributes['ReportingDescriptor.id'] = $ruleId;
                }

                if (!empty($attributes)) {
                    $errors[$filename][] = $attributes;
                }
            }
        }

        return [
            'files' => array_unique($filenames),
            'errors' => $errors,
            'rules' => $rules,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function fromInternal(Report $report): array
    {
        $files = [];
        $errors = [];
        $rules = [];

        /** @var RuleViolation[] $violations */
        $violations = $report->getRuleViolations();

        foreach ($violations as $violation) {
            $filename = $violation->getFileName();
            $files[] = $filename;

            $rule = $violation->getRule();
            $ruleRef = str_replace(' ', '', $rule->getRuleSetName()) . '/' . $rule->getName();
            $helpText = trim(str_replace("\n", ' ', $rule->getDescription()));
            $rules[$ruleRef] = [
                'name' => $rule->getName(),
                'shortDescription' => $rule->getRuleSetName() . ': ' . $rule->getName(),
                'messageStrings' => ['default' => trim($rule->getMessage())],
                'help' => [
                    'text' => $helpText,
                ],
                'helpUri' => $rule->getExternalInfoUrl(),
                'properties' => [
                    'ruleSet' => $rule->getRuleSetName(),
                    'priority' => $rule->getPriority(),
                ]
            ];

            $examples = $rule->getExamples();
            if (!empty($examples)) {
                $rules[$ruleRef]['help']['markdown'] =
                    $helpText .
                    "\n\n### Example\n\n```php\n" .
                    implode("\n```\n\n```php\n", array_map('trim', $examples)) . "\n```"
                ;
            }

            $since = $rule->getSince();
            if (!empty($since)) {
                $rules[$ruleRef]['properties']['since'] = 'PHPMD ' . $since;
            }

            $attributes = [
                'ReportingDescriptor.id' => $ruleRef,
                'Result.message' => $violation->getDescription(),
                'Result.message.id' => 'default',
                'Result.message.arguments' => $violation->getArgs(),
                'Region.startLine' => $violation->getBeginLine(),
                'Region.endLine' => $violation->getEndLine(),
            ];
            $errors[$filename][] = $attributes;
        }

        /** @var ProcessingError[] $processingErrors */
        $processingErrors = $report->getErrors();

        foreach ($processingErrors as $error) {
            $filename = $error->getFile();
            $files[] = $filename;

            $attributes = [
                'Result.message' => $error->getMessage(),
                'Result.level' => 'error',
            ];
            $errors[$filename][] = $attributes;
        }

        return [
            'files' => array_unique($files),
            'errors' => $errors,
            'rules' => $rules,
        ];
    }
}
