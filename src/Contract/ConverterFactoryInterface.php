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
    public function create(
        string $name,
        ?SerializerFactory $serializerFactory = null,
        bool $prettyPrint = false
    ): ConverterInterface;
}
