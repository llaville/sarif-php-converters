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

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class PhpLintNormalizer implements NormalizerInterface
{
    public function getSupportedFormats(): array
    {
        return [
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
            'error' => 'Result.message',
            'line' => 'Region.startLine',
        ];

        // internal format (legacy)
        return new ArrayObject($this->fromInternal($data, $mapping, $context));
    }

    /**
     * @param array<string, mixed> $data
     * @param array<string, string> $mapping
     * @param array<string, mixed> $context Options available to the normalizer
     * @return array{
     *     files: string[],
     *     errors: array<string, array<int, array<string, string>>>,
     *     rules: array<string, mixed>
     * }
     */
    private function fromInternal(array $data, array $mapping, array $context): array
    {
        $ruleId = $context['rulePrefix'] . '101';

        $errors = [];

        foreach ($data as $file => $failure) {
            $attributes = [];
            foreach ($failure as $attr => $value) {
                $key = $mapping[$attr] ?? '.' . $attr;
                $attributes[$key] = $value;
            }
            $attributes['ReportingDescriptor.id'] = $ruleId;
            $errors[$file][] = $attributes;
        }

        return [
            'files' => array_keys($data),
            'errors' => $errors,
            'rules' => [
                $ruleId => [
                    'shortDescription' => 'Syntax error',
                    'fullDescription' => 'Syntax error detected when lint a file',
                    'help' => 'https://www.php.net/manual/en/features.commandline.options.php',
                    'helpUri' => 'https://www.php.net/manual/en/langref.php',
                ],
            ],
        ];
    }
}
