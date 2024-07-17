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
    public const BUILTIN_SOURCE_ECS = 'ecs';
    public const BUILTIN_SOURCE_PHAN = 'phan';
    public const BUILTIN_SOURCE_PHPCS = 'phpcs';
    public const BUILTIN_SOURCE_PHPLINT = 'phplint';
    public const BUILTIN_SOURCE_PHPMD = 'phpmd';
    public const BUILTIN_SOURCE_PHPSTAN = 'phpstan';
    public const BUILTIN_SOURCE_TWIG_CS_FIXER = 'twigcs-fixer';

    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function create(string $name, iterable $normalizers): SourceInterface;
}
