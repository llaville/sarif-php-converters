<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Contract;

use Iterator;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
interface SourceInterface
{
    /**
     * Alias that identify the Source class of data
     */
    public function getName(): string;

    /**
     * Alias for {getAttribute('files')}
     */
    public function getFiles(): Iterator;

    /**
     * Alias for {getAttribute('errors')}
     */
    public function getErrors(): Iterator;

    /**
     * Alias for {getAttribute('rules')}
     */
    public function getRules(): Iterator;

    /**
     * Returns any normalized data group.
     */
    public function getAttribute(string $name): Iterator;

    /**
     * Normalizes the data source.
     *
     * @param mixed $data Data to normalize the source
     * @param string $format Format that the normalizer should support
     * @param array<string, mixed> $context Options available to the normalizer
     */
    public function normalize($data, string $format, array $context): void;
}
