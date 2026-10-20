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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function json_encode;

use const JSON_THROW_ON_ERROR;

#[CoversClass(ComposerNormalizer::class)]
final class ComposerNormalizerTest extends TestCase
{
    /**
     * @return iterable<string, array{?string, string}>
     */
    public static function severityProvider(): iterable
    {
        yield 'low' => ['low', 'none'];
        yield 'medium' => ['medium', 'note'];
        yield 'high' => ['high', 'warning'];
        yield 'critical' => ['critical', 'error'];
        yield 'missing (Drupal security advisories)' => [null, 'warning'];
    }

    #[DataProvider('severityProvider')]
    public function testSeverityIsMappedToSarifLevel(?string $severity, string $expectedLevel): void
    {
        $normalized = (new ComposerNormalizer())->normalize(
            $this->auditReport($severity),
            NormalizerInterface::FORMAT_JSON,
            []
        );

        $this->assertNotNull($normalized);
        $errors = $normalized['errors'];
        $rules = $normalized['rules'];
        $this->assertSame($expectedLevel, $errors['composer.json'][0]['Result.level']);
        $this->assertSame($expectedLevel, $rules['CA0001']['defaultConfiguration']['level']);
    }

    private function auditReport(?string $severity): string
    {
        return json_encode([
            'advisories' => [
                'drupal/core' => [
                    [
                        'advisoryId' => 'SA-CORE-2026-014',
                        'packageName' => 'drupal/core',
                        'affectedVersions' => '>= 11.4.0 < 11.4.10',
                        'title' => 'Drupal core - Critical - Remote code execution - SA-CORE-2026-014',
                        'cve' => 'CVE-2026-108097',
                        'link' => 'https://www.drupal.org/sa-core-2026-014',
                        'reportedAt' => '2026-10-14T16:10:00+00:00',
                        'sources' => [['name' => 'Drupal', 'remoteId' => 'SA-CORE-2026-014']],
                        'severity' => $severity,
                    ],
                ],
            ],
            'abandoned' => [],
        ], JSON_THROW_ON_ERROR);
    }
}
