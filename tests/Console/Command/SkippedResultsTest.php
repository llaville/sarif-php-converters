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

use function chdir;
use function file_put_contents;
use function getcwd;
use function glob;
use function json_encode;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function uniqid;
use function unlink;

use const JSON_THROW_ON_ERROR;

#[CoversClass(ConvertCommand::class)]
#[CoversClass(AbstractConverter::class)]
final class SkippedResultsTest extends TestCase
{
    private string $originalWorkingDirectory;
    private string $workingDirectory;

    protected function setUp(): void
    {
        $this->originalWorkingDirectory = (string) getcwd();
        $this->workingDirectory = sys_get_temp_dir() . '/sarif-convert-' . uniqid();
        mkdir($this->workingDirectory);
        chdir($this->workingDirectory);

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

    public function testResultsForAMissingFileAreReportedOnStderr(): void
    {
        $tester = $this->convertAuditReport();

        $this->assertStringContainsString(
            'Skipped 1 result(s) for "composer.json": file not found in working directory',
            $tester->getErrorOutput()
        );
    }

    public function testNoWarningWhenTheFileExists(): void
    {
        file_put_contents('composer.json', '{}');

        $tester = $this->convertAuditReport();

        $this->assertSame('', $tester->getErrorOutput());
    }

    private function convertAuditReport(): CommandTester
    {
        $tester = new CommandTester(new ConvertCommand());
        $tester->execute(
            [
                'source' => 'composer',
                '--input-format' => 'json',
                '--input-file' => 'audit.json',
                '--output-file' => 'audit.sarif',
            ],
            ['capture_stderr_separately' => true]
        );

        return $tester;
    }

    private function auditReport(): string
    {
        return json_encode([
            'advisories' => [
                'enshrined/svg-sanitize' => [
                    [
                        'advisoryId' => 'PKSA-8j6w-3kr7-s9xk',
                        'packageName' => 'enshrined/svg-sanitize',
                        'affectedVersions' => '<=0.22.0',
                        'title' => 'Stored XSS via DTD Entity / HTML5 Named Character Reference Collision',
                        'cve' => 'CVE-2026-107379',
                        'link' => 'https://github.com/advisories/GHSA-v383-3rw5-q8rf',
                        'reportedAt' => '2026-10-08T19:41:05+00:00',
                        'sources' => [['name' => 'GitHub', 'remoteId' => 'GHSA-v383-3rw5-q8rf']],
                        'severity' => 'medium',
                    ],
                ],
            ],
            'abandoned' => [],
        ], JSON_THROW_ON_ERROR);
    }
}
