<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Contract;

use ArrayObject;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
interface NormalizerInterface
{
    public const FORMAT_SARIF = 'sarif';
    public const FORMAT_JSON = 'json';
    public const FORMAT_XML = 'xml';
    public const FORMAT_CHECKSTYLE = 'checkstyle';
    public const FORMAT_INTERNAL = 'internal';

    /**
     * @return string[]
     */
    public function getSupportedFormats(): array;

    /**
     * Checks if the format is supported by the current normalizer
     */
    public function isSupportedFormat(string $format): bool;

    /**
     * Normalizes the data source.
     *
     * @param mixed $data Data to normalize
     * @param string $format Format that the normalizer should support
     * @param array<string, mixed> $context Options available to the normalizer
     * @return ArrayObject<string, array{files: mixed, errors: mixed, rules: mixed}>|null
     */
    public function normalize($data, string $format, array $context): ?ArrayObject;
}
