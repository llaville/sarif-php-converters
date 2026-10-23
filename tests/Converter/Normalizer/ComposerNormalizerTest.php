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
use PHPUnit\Framework\TestCase;

use ArrayObject;

use function json_encode;

use const JSON_THROW_ON_ERROR;

#[CoversClass(ComposerNormalizer::class)]
final class ComposerNormalizerTest extends TestCase
{
    public function testSeverityIsMappedToSarifLevel(): void
    {
        $normalized = $this->normalize('critical');

        $this->assertSame('error', $normalized['errors']['composer.json'][0]['Result.level']);
        $this->assertSame('error', $normalized['rules']['CA0001']['defaultConfiguration']['level']);
    }

    public function testMissingSeverityLeavesTheLevelToTheConverterDefault(): void
    {
        // Drupal security advisories come without severity
        $normalized = $this->normalize(null);

        $this->assertArrayNotHasKey('Result.level', $normalized['errors']['composer.json'][0]);
        $this->assertArrayNotHasKey('level', $normalized['rules']['CA0001']['defaultConfiguration']);
    }

    /**
     * @return ArrayObject<string, mixed>
     */
    private function normalize(?string $severity): ArrayObject
    {
        $report = json_encode([
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

        $normalized = (new ComposerNormalizer())->normalize($report, NormalizerInterface::FORMAT_JSON, []);
        $this->assertNotNull($normalized);

        return $normalized;
    }
}
