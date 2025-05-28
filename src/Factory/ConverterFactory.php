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
        array $options,
        ?SerializerFactory $serializerFactory = null
    ): ConverterInterface {
        switch (strtolower($name)) {
            case ConverterFactoryInterface::BUILTIN_CONVERTER_COMPOSER:
                return $this->createComposer($options, $serializerFactory);
            case ConverterFactoryInterface::BUILTIN_CONVERTER_ECS:
                return $this->createEasyCodingStandard($options, $serializerFactory);
            case ConverterFactoryInterface::BUILTIN_CONVERTER_PHAN:
                return $this->createPhan($options, $serializerFactory);
            case ConverterFactoryInterface::BUILTIN_CONVERTER_PHPCS:
                return $this->createCodeSniffer($options, $serializerFactory);
            case ConverterFactoryInterface::BUILTIN_CONVERTER_PHPCSFIXER:
                return $this->createCsFixer($options, $serializerFactory);
            case ConverterFactoryInterface::BUILTIN_CONVERTER_PHPLINT:
                return $this->createLinter($options, $serializerFactory);
            case ConverterFactoryInterface::BUILTIN_CONVERTER_PHPMD:
                return $this->createMessDetector($options, $serializerFactory);
            case ConverterFactoryInterface::BUILTIN_CONVERTER_PHPSTAN:
                return $this->createStan($options, $serializerFactory);
            case ConverterFactoryInterface::BUILTIN_CONVERTER_RECTOR:
                return $this->createRector($options, $serializerFactory);
            case ConverterFactoryInterface::BUILTIN_CONVERTER_TWIG_CS_FIXER:
                return $this->createTwigCsFixer($options, $serializerFactory);
        }

        throw new OutOfBoundsException(sprintf('Converter "%s" not found.', $name));
    }

    /**
     * @param array{
     *     format_output: bool,
     *     include_code_snippets?: bool,
     *     include_context_region?: bool
     * } $options
     */
    public function createComposer(array $options, ?SerializerFactory $serializerFactory = null): ConverterInterface
    {
        return new Converter\ComposerConverter($options, $serializerFactory);
    }

    /**
     * @param array{
     *     format_output: bool,
     *     include_code_snippets?: bool,
     *     include_context_region?: bool
     * } $options
     */
    public function createEasyCodingStandard(array $options, ?SerializerFactory $serializerFactory = null): ConverterInterface
    {
        return new Converter\EcsConverter($options, $serializerFactory);
    }

    /**
     * @param array{
     *     format_output: bool,
     *     include_code_snippets?: bool,
     *     include_context_region?: bool
     * } $options
     */
    public function createPhan(array $options, ?SerializerFactory $serializerFactory = null): ConverterInterface
    {
        return new Converter\PhanConverter($options, $serializerFactory);
    }

    /**
     * @param array{
     *     format_output: bool,
     *     include_code_snippets?: bool,
     *     include_context_region?: bool
     * } $options
     */
    public function createCodeSniffer(array $options, ?SerializerFactory $serializerFactory = null): ConverterInterface
    {
        return new Converter\PhpCsConverter($options, $serializerFactory);
    }

    /**
     * @param array{
     *     format_output: bool,
     *     include_code_snippets?: bool,
     *     include_context_region?: bool
     * } $options
     */
    public function createCsFixer(array $options, ?SerializerFactory $serializerFactory = null): ConverterInterface
    {
        return new Converter\PhpCsFixerConverter($options, $serializerFactory);
    }

    /**
     * @param array{
     *     format_output: bool,
     *     include_code_snippets?: bool,
     *     include_context_region?: bool
     * } $options
     */
    public function createLinter(array $options, ?SerializerFactory $serializerFactory = null): ConverterInterface
    {
        return new Converter\PhpLintConverter($options, $serializerFactory);
    }

    /**
     * @param array{
     *     format_output: bool,
     *     include_code_snippets?: bool,
     *     include_context_region?: bool
     * } $options
     */
    public function createMessDetector(array $options, ?SerializerFactory $serializerFactory = null): ConverterInterface
    {
        return new Converter\PhpMdConverter($options, $serializerFactory);
    }

    /**
     * @param array{
     *     format_output: bool,
     *     include_code_snippets?: bool,
     *     include_context_region?: bool
     * } $options
     */
    public function createStan(array $options, ?SerializerFactory $serializerFactory = null): ConverterInterface
    {
        return new Converter\PhpStanConverter($options, $serializerFactory);
    }

    /**
     * @param array{
     *     format_output: bool,
     *     include_code_snippets?: bool,
     *     include_context_region?: bool
     * } $options
     */
    public function createRector(array $options, ?SerializerFactory $serializerFactory = null): ConverterInterface
    {
        return new Converter\RectorConverter($options, $serializerFactory);
    }

    /**
     * @param array{
     *     format_output: bool,
     *     include_code_snippets?: bool,
     *     include_context_region?: bool
     * } $options
     */
    public function createTwigCsFixer(array $options, ?SerializerFactory $serializerFactory = null): ConverterInterface
    {
        return new Converter\TwigCsFixerConverter($options, $serializerFactory);
    }
}
