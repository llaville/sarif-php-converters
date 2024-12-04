<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Contract;

use Bartlett\Sarif\Factory\SerializerFactory;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
interface ConverterFactoryInterface
{
    public const BUILTIN_CONVERTER_ECS = 'ecs';
    public const BUILTIN_CONVERTER_PHAN = 'phan';
    public const BUILTIN_CONVERTER_PHPCS = 'phpcs';
    public const BUILTIN_CONVERTER_PHPCSFIXER = 'phpcs-fixer';
    public const BUILTIN_CONVERTER_PHPLINT = 'phplint';
    public const BUILTIN_CONVERTER_PHPMD = 'phpmd';
    public const BUILTIN_CONVERTER_PHPSTAN = 'phpstan';
    public const BUILTIN_CONVERTER_RECTOR = 'rector';
    public const BUILTIN_CONVERTER_TWIG_CS_FIXER = 'twigcs-fixer';

    /**
     * @param array{
     *     format_output: bool,
     *     include_code_snippets?: bool,
     *     include_context_region?: bool
     * } $options
     */
    public function create(
        string $name,
        array $options,
        ?SerializerFactory $serializerFactory = null
    ): ConverterInterface;
}
