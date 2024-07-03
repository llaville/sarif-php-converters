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
        switch (strtolower($name)) {
            case SourceFactoryInterface::BUILTIN_SOURCE_PHPCS:
                return $this->createCodeSniffer($normalizers);
            case SourceFactoryInterface::BUILTIN_SOURCE_PHPSTAN:
                return $this->createStan($normalizers);
        }

        throw new OutOfBoundsException(sprintf('Source "%s" not found.', $name));
    }

    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function createCodeSniffer(iterable $normalizers): SourceInterface
    {
        $normalizers = (array) $normalizers;
        $normalizers[] = new Normalizer\PhpCsNormalizer();
        return new Source\PhpCsSource($normalizers);
    }

    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function createStan(iterable $normalizers): SourceInterface
    {
        $normalizers = (array) $normalizers;
        $normalizers[] = new Normalizer\PhpStanNormalizer();
        return new Source\PhpStanSource($normalizers);
    }
}
