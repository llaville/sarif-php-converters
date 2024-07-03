<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Factory;

use Bartlett\Sarif\Contract\ConverterFactoryInterface;
use Bartlett\Sarif\Contract\ConverterInterface;
use Bartlett\Sarif\Converter;

use OutOfBoundsException;
use function sprintf;
use function strtolower;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class ConverterFactory implements ConverterFactoryInterface
{
    public function create(
        string $name,
        ?SerializerFactory $serializerFactory = null,
        bool $prettyPrint = false
    ): ConverterInterface {
        throw new OutOfBoundsException(sprintf('Converter "%s" not found.', $name));
    }
}
