<?php

declare(strict_types=1);

/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bartlett\Sarif\Tests\Converter\Normalizer;

use Bartlett\Sarif\Contract\NormalizerInterface;
use Bartlett\Sarif\Converter\Normalizer\ComposerNormalizer;
use Bartlett\Sarif\Converter\Normalizer\MagoNormalizer;
use Bartlett\Sarif\Converter\Normalizer\PhpCsNormalizer;
use Bartlett\Sarif\Converter\Normalizer\SarifNormalizer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use JsonException;

#[CoversClass(ComposerNormalizer::class)]
#[CoversClass(MagoNormalizer::class)]
#[CoversClass(PhpCsNormalizer::class)]
#[CoversClass(SarifNormalizer::class)]
final class InvalidJsonInputTest extends TestCase
{
    /**
     * A tool's report with a warning in front of it, as it arrives when stderr is piped into the converter.
     */
    private const PREFIXED_REPORT = "some warning on stderr\n{\"advisories\": {}}";

    /**
     * @return iterable<string, array{NormalizerInterface, string}>
     */
    public static function normalizerProvider(): iterable
    {
        yield 'composer' => [new ComposerNormalizer(), NormalizerInterface::FORMAT_JSON];
        yield 'mago' => [new MagoNormalizer(), NormalizerInterface::FORMAT_GITLAB];
        yield 'phpcs' => [new PhpCsNormalizer(), NormalizerInterface::FORMAT_JSON];
        yield 'sarif' => [new SarifNormalizer(), NormalizerInterface::FORMAT_SARIF];
    }

    #[DataProvider('normalizerProvider')]
    public function testInvalidJsonIsRejected(NormalizerInterface $normalizer, string $format): void
    {
        $this->expectException(JsonException::class);

        $normalizer->normalize(self::PREFIXED_REPORT, $format, []);
    }
}
