<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Contract;

use Bartlett\Sarif\Definition;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
interface ConverterInterface
{
    /**
     * Alias that identify the Converter class
     */
    public function getName(): string;

    public function toolDriver(): Definition\ToolComponent;

    /**
     * @return Definition\ToolComponent[]
     */
    public function toolExtensions(): array;

    /**
     * @param iterable<string, mixed> $defined
     * @return array<Definition\ReportingDescriptor>
     */
    public function rules(iterable $defined): array;

    /**
     * @param iterable<string, array<int, mixed>> $normalized
     * @return void
     */
    public function results(iterable $normalized): void;

    /**
     * @return Definition\Invocation[]
     */
    public function invocations(?Definition\PropertyBag $properties = null): array;

    public function automationDetails(): Definition\RunAutomationDetails;

    /**
     * @param iterable<Definition\Invocation> $invocations
     * @param iterable<string, array<string>> $rules
     * @return Definition\Run
     */
    public function run(iterable $invocations, iterable $rules): Definition\Run;

    /**
     * @param Definition\Run[] $runs
     */
    public function sarifLog(array $runs = [], string $version = '2.1.0'): ?string;
}
