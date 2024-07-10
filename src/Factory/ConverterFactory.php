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
        switch (strtolower($name)) {
            case ConverterFactoryInterface::BUILTIN_CONVERTER_ECS:
                return $this->createEasyCodingStandard($serializerFactory, $prettyPrint);
            case ConverterFactoryInterface::BUILTIN_CONVERTER_PHPCS:
                return $this->createCodeSniffer($serializerFactory, $prettyPrint);
            case ConverterFactoryInterface::BUILTIN_CONVERTER_PHPLINT:
                return $this->createLinter($serializerFactory, $prettyPrint);
            case ConverterFactoryInterface::BUILTIN_CONVERTER_PHPMD:
                return $this->createMessDetector($serializerFactory, $prettyPrint);
            case ConverterFactoryInterface::BUILTIN_CONVERTER_PHPSTAN:
                return $this->createStan($serializerFactory, $prettyPrint);
        }

        throw new OutOfBoundsException(sprintf('Converter "%s" not found.', $name));
    }

    public function createEasyCodingStandard(?SerializerFactory $serializerFactory = null, ?bool $prettyPrint = false): ConverterInterface
    {
        return new Converter\EcsConverter($serializerFactory, $prettyPrint);
    }

    public function createCodeSniffer(?SerializerFactory $serializerFactory = null, bool $prettyPrint = false): ConverterInterface
    {
        return new Converter\PhpCsConverter($serializerFactory, $prettyPrint);
    }

    public function createLinter(?SerializerFactory $serializerFactory = null, bool $prettyPrint = false): ConverterInterface
    {
        return new Converter\PhpLintConverter($serializerFactory, $prettyPrint);
    }

    public function createMessDetector(?SerializerFactory $serializerFactory = null, bool $prettyPrint = false): ConverterInterface
    {
        return new Converter\PhpMdConverter($serializerFactory, $prettyPrint);
    }

    public function createStan(?SerializerFactory $serializerFactory = null, bool $prettyPrint = false): ConverterInterface
    {
        return new Converter\PhpStanConverter($serializerFactory, $prettyPrint);
    }
}
