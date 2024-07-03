<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Contract;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
interface SourceFactoryInterface
{
    public const BUILTIN_SOURCE_PHPCS = 'phpcs';
    public const BUILTIN_SOURCE_PHPSTAN = 'phpstan';

    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function create(string $name, iterable $normalizers): SourceInterface;
}
