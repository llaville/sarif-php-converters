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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(ConvertCommand::class)]
final class ConvertCommandTest extends TestCase
{
    public function testErrorWithoutCodeExitsWithFailure(): void
    {
        $tester = new CommandTester(new ConvertCommand());

        // "Input format is required" is raised without an exception code
        $exitCode = $tester->execute(['source' => 'composer']);

        $this->assertSame(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Input format is required', $tester->getDisplay());
    }

    public function testErrorWithCodeExitsWithThatCode(): void
    {
        $tester = new CommandTester(new ConvertCommand());

        // "File not found" is raised with code 2
        $exitCode = $tester->execute([
            'source' => 'composer',
            '--input-format' => 'json',
            '--input-file' => __DIR__ . '/does-not-exist.json',
        ]);

        $this->assertSame(Command::INVALID, $exitCode);
    }
}
