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
use function in_array;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class TwigCsFixerNormalizer implements NormalizerInterface
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

        // internal format (legacy)
        return new ArrayObject($this->fromInternal($data, $context));
    }

    /**
     * @param mixed $data
     * @param array<string, mixed> $context Options available to the normalizer
     * @return array{
     *     files: string[],
     *     errors: array<string, array<int, array<string, bool|int|string|null>>>,
     *     rules: array<string, mixed>
     * }
     */
    private function fromInternal($data, array $context): array
    {
        $files = [];
        $errors = [];
        $rules = [];

        return [
            'files' => $files,
            'errors' => $errors,
            'rules' => $rules,
        ];
    }
}
