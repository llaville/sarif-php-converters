<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Normalizer;

use Bartlett\Sarif\Contract\NormalizerInterface;

use ArrayObject;
use function array_keys;
use function in_array;
use function json_decode;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class PhpCsNormalizer implements NormalizerInterface
{
    public function getSupportedFormats(): array
    {
        return [
            NormalizerInterface::FORMAT_JSON,
            NormalizerInterface::FORMAT_INTERNAL,
        ];
    }

    public function normalize($data, string $format, array $context): ?ArrayObject
    {
        if (!in_array($format, $this->getSupportedFormats())) {
            return null;
        }

        // default common mapping for all formats
        $mapping = [
            'message' => 'Result.message',
            'source' => 'ReportingDescriptor.id',
            'fixable' => 'Result.properties.fixable',
            'severity' => 'Result.properties.severity',
            'type' => 'Result.level',
            'line' => 'Region.startLine',
            'column' => 'Region.startColumn',
        ];

        if ('json' === $format) {
            return new ArrayObject($this->fromJson($data, $mapping));
        }

        // internal format (legacy)
        return new ArrayObject($this->fromInternal($data, $mapping));
    }

    /**
     * @param array<string, array<int, mixed>|string> $data
     * @param array<string, string> $mapping
     * @return array<string, mixed>
     */
    private function fromInternal(array $data, array $mapping): array
    {
        $filename = $data['filename'];
        $errors = [];
        $rules = [];
        $totals = [
            'errors' => 0,
            'warnings' => 0,
            'fixable' => 0,
        ];

        foreach ($data['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $totals['errors'] += $data['errors'];
                    $totals['warnings'] += $data['warnings'];
                    $totals['fixable'] += $data['fixable'];

                    $error['line'] = $line;
                    $error['column'] = $column;

                    $attributes = [];
                    foreach ($error as $attr => $value) {
                        $key = $mapping[$attr] ?? '.' . $attr;
                        $attributes[$key] = ('Result.level' === $key) ? strtolower($value) : $value;
                    }
                    $errors[$filename][] = $attributes;

                    if (!isset($rules[$error['source']])) {
                        $rules[$error['source']] = ['properties' => ['frequency' => 0]];
                    }

                    $rules[$error['source']]['properties']['frequency'] += 1;
                }
            }
        }

        return [
            // direct values
            'totals' => $totals,
            'files' => [$filename],
            // custom values
            'errors' => $errors,
            'rules' => $rules,
        ];
    }

    /**
     * @param array<string, string> $mapping
     * @return array<string, mixed>
     */
    private function fromJson(string $data, array $mapping): array
    {
        // $data is already JSON encoded
        $collected = json_decode($data, true);

        $errors = [];
        $rules = [];

        foreach ($collected['files'] as $filename => $values) {
            foreach ($values['messages'] as $message) {
                $attributes = [];
                foreach ($message as $attr => $value) {
                    $key = $mapping[$attr] ?? '.' . $attr;
                    $attributes[$key] = ('Result.level' === $key) ? strtolower($value) : $value;
                }
                $errors[$filename][] = $attributes;

                if (!isset($rules[$message['source']])) {
                    $rules[$message['source']] = ['properties' => ['frequency' => 0]];
                }
                $rules[$message['source']]['properties']['frequency'] += 1;
            }
        }

        return [
            // direct values
            'totals' => $collected['totals'],
            'files' => array_keys($collected['files']),
            // custom values
            'errors' => $errors,
            'rules' => $rules,
        ];
    }
}
