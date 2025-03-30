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
 * @template TData
 * @author Laurent Laville
 * @since Release 1.0.0
 */
abstract class AbstractNormalizer implements NormalizerInterface
{
    public function getSupportedFormats(): array
    {
        return [
            NormalizerInterface::FORMAT_INTERNAL,
        ];
    }

    /**
     * @param TData $data
     */
    public function normalize($data, string $format, array $context): ?ArrayObject
    {
        if (!in_array($format, $this->getSupportedFormats())) {
            return null;
        }

        // internal format (legacy)
        return new ArrayObject($this->fromInternal($data, $context));
    }

    /**
     * @param TData $data
     * @param array<string, mixed> $context Options available to the normalizer
     * @param array{}|array<string, string> $mapping From-To convert's mapping
     * @return array{files: mixed, errors: mixed, rules: mixed}
     */
    protected function fromInternal($data, array $context, array $mapping = []): array
    {
        return [
            'files' => [],
            'errors' => [],
            'rules' => [],
        ];
    }
}
