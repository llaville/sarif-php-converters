<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Factory;

use Bartlett\Sarif\Contract\NormalizerInterface;
use Bartlett\Sarif\Contract\SourceFactoryInterface;
use Bartlett\Sarif\Contract\SourceInterface;
use Bartlett\Sarif\Converter\Normalizer;
use Bartlett\Sarif\Converter\Source;

use OutOfBoundsException;
use function sprintf;
use function strtolower;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class SourceFactory implements SourceFactoryInterface
{
    public function create(string $name, iterable $normalizers): SourceInterface
    {
        throw new OutOfBoundsException(sprintf('Source "%s" not found.', $name));
    }
}
