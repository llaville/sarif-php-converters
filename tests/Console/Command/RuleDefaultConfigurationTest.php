<?php

declare(strict_types=1);

/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bartlett\Sarif\Tests\Console\Command;

use Bartlett\Sarif\Console\Command\ConvertCommand;
use Bartlett\Sarif\Converter\AbstractConverter;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use Symfony\Component\Console\Tester\CommandTester;

use JsonException;

use function chdir;
use function file_get_contents;
use function file_put_contents;
use function getcwd;
use function glob;
use function json_decode;
use function json_encode;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

use const JSON_THROW_ON_ERROR;

#[CoversClass(ConvertCommand::class)]
#[CoversClass(AbstractConverter::class)]
final class RuleDefaultConfigurationTest extends TestCase
{
    private string $originalWorkingDirectory;
    private string $workingDirectory;

    protected function setUp(): void
    {
        $this->originalWorkingDirectory = (string) getcwd();
        $this->workingDirectory = sys_get_temp_dir() . '/sarif-convert-' . uniqid();
        mkdir($this->workingDirectory);
        chdir($this->workingDirectory);

        file_put_contents('composer.json', '{}');
        file_put_contents('audit.json', $this->auditReport());
    }

    protected function tearDown(): void
    {
        chdir($this->originalWorkingDirectory);

        foreach ((array) glob($this->workingDirectory . '/*') as $file) {
            unlink((string) $file);
        }
        rmdir($this->workingDirectory);
    }

    /**
     * @throws JsonException
     */
    public function testRuleWithoutParametersHasNoParametersProperty(): void
    {
        // SARIF requires an object here; an empty PropertyBag serializes as [],
        // which validators such as github/codeql-action/upload-sarif reject
        $tester = new CommandTester(new ConvertCommand());
        $tester->execute([
            'source' => 'composer',
            '--input-format' => 'json',
            '--input-file' => 'audit.json',
            '--output-file' => 'audit.sarif',
        ]);

        $sarif = json_decode((string) file_get_contents('audit.sarif'), true, 512, JSON_THROW_ON_ERROR);
        $defaultConfiguration = $sarif['runs'][0]['tool']['driver']['rules'][0]['defaultConfiguration'];

        $this->assertArrayNotHasKey('parameters', $defaultConfiguration);
    }

    /**
     * @throws JsonException
     */
    private function auditReport(): string
    {
        return json_encode([
            'advisories' => [
                'symfony/mime' => [
                    [
                        'advisoryId' => 'PKSA-wtxr-p26d-nn42',
                        'packageName' => 'symfony/mime',
                        'affectedVersions' => '>=7.4.0,<7.4.22',
                        'title' => 'CVE-2026-45070: Email Header Injection via Non-Token Characters in Mime Parameter Names',
                        'cve' => 'CVE-2026-45070',
                        'link' => 'https://symfony.com/cve-2026-45070',
                        'reportedAt' => '2026-10-28T12:00:00+00:00',
                        'sources' => [['name' => 'GitHub', 'remoteId' => 'GHSA-wtxr-p26d-nn42']],
                        'severity' => 'medium',
                    ],
                ],
            ],
            'abandoned' => [],
        ], JSON_THROW_ON_ERROR);
    }
}
