<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Factory;

use Bartlett\Sarif\Contract\HelpUriCaseConverterInterface;
use Bartlett\Sarif\Contract\NormalizerInterface;
use Bartlett\Sarif\Contract\SourceFactoryInterface;
use Bartlett\Sarif\Contract\SourceInterface;
use Bartlett\Sarif\Converter\Case;
use Bartlett\Sarif\Converter\Normalizer;
use Bartlett\Sarif\Converter\Source;

use Jawira\CaseConverter\CaseConverterInterface;
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
            case SourceFactoryInterface::BUILTIN_SOURCE_COMPOSER:
                return $this->createComposer($normalizers);
            case SourceFactoryInterface::BUILTIN_SOURCE_ECS:
                return $this->createEasyCodingStandard($normalizers);
            case SourceFactoryInterface::BUILTIN_SOURCE_PHAN:
                return $this->createPhan($normalizers);
            case SourceFactoryInterface::BUILTIN_SOURCE_PHPCS:
                return $this->createCodeSniffer($normalizers);
            case SourceFactoryInterface::BUILTIN_SOURCE_PHPCSFIXER:
                return $this->createCsFixer($normalizers);
            case SourceFactoryInterface::BUILTIN_SOURCE_PHPLINT:
                return $this->createLinter($normalizers);
            case SourceFactoryInterface::BUILTIN_SOURCE_PHPMD:
                return $this->createMessDetector($normalizers);
            case SourceFactoryInterface::BUILTIN_SOURCE_PHPSTAN:
                return $this->createStan($normalizers);
            case SourceFactoryInterface::BUILTIN_SOURCE_RECTOR:
                return $this->createRector($normalizers);
            case SourceFactoryInterface::BUILTIN_SOURCE_TWIG_CS_FIXER:
                return $this->createTwigCsFixer($normalizers);
        }

        throw new OutOfBoundsException(sprintf('Source "%s" not found.', $name));
    }

    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function createComposer(iterable $normalizers): SourceInterface
    {
        $normalizers = (array) $normalizers;
        $normalizers[] = new Normalizer\ComposerNormalizer();
        return new Source\ComposerSource($normalizers);
    }

    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function createEasyCodingStandard(iterable $normalizers, ?CaseConverterInterface $caseConverter = null): SourceInterface
    {
        $normalizers = (array) $normalizers;
        $normalizers[] = new Normalizer\EcsNormalizer();
        $caseConverter = new Case\EcsHelpUriCaseConverter($caseConverter);
        return new Source\EcsSource($normalizers, $caseConverter);
    }

    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function createPhan(iterable $normalizers): SourceInterface
    {
        $normalizers = (array) $normalizers;
        $normalizers[] = new Normalizer\PhanNormalizer();
        return new Source\PhanSource($normalizers);
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
    public function createCsFixer(iterable $normalizers): SourceInterface
    {
        $normalizers = (array) $normalizers;
        $normalizers[] = new Normalizer\PhpCsFixerNormalizer();
        return new Source\PhpCsFixerSource($normalizers);
    }

    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function createLinter(iterable $normalizers): SourceInterface
    {
        $normalizers = (array) $normalizers;
        $normalizers[] = new Normalizer\PhpLintNormalizer();
        return new Source\PhpLintSource($normalizers);
    }

    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function createMessDetector(iterable $normalizers): SourceInterface
    {
        $finalNormalizers = [];
        foreach ($normalizers as $normalizer) {
            if ($normalizer instanceof Normalizer\CheckstyleNormalizer) {
                // PHPMD uses non-standard attributes from Checkstyle format,
                // so we cannot use the default CheckstyleNormalizer
                continue;
            }
            $finalNormalizers[] = $normalizer;
        }
        $finalNormalizers[] = new Normalizer\PhpMdNormalizer();
        $finalNormalizers[] = new Normalizer\SarifNormalizer();
        return new Source\PhpMdSource($finalNormalizers);
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

    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function createRector(iterable $normalizers): SourceInterface
    {
        $normalizers = (array) $normalizers;
        $normalizers[] = new Normalizer\RectorNormalizer();
        return new Source\RectorSource($normalizers);
    }

    /**
     * @param iterable<NormalizerInterface> $normalizers
     */
    public function createTwigCsFixer(iterable $normalizers): SourceInterface
    {
        $normalizers = (array) $normalizers;
        $normalizers[] = new Normalizer\TwigCsFixerNormalizer();
        return new Source\TwigCsFixerSource($normalizers);
    }
}
