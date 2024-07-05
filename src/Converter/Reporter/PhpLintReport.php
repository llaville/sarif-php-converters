<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Reporter;

use Bartlett\Sarif\Contract\ConverterInterface;
use Bartlett\Sarif\Contract\SourceInterface;
use Bartlett\Sarif\Converter\PhpLintConverter;
use Bartlett\Sarif\Converter\Source\PhpLintSource;

use Overtrue\PHPLint\Output\LinterOutput;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class PhpLintReport extends AbstractReporter
{
    private string $rulePrefix;

    public function __construct(?ConverterInterface $converter = null, ?SourceInterface $source = null)
    {
        $this->converter = $converter ?? new PhpLintConverter();
        $this->source = $source ?? new PhpLintSource();

        $this->rulePrefix = 'PHPLINT';
    }

    public function format(LinterOutput $results): void
    {
        $this->source->normalize($results->getFailures(), 'internal', ['rulePrefix' => $this->rulePrefix]);

        $this->converter->results($this->source->getErrors());

        $run = $this->converter->run($this->converter->invocations(), $this->getRules());

        echo $this->converter->sarifLog([$run]);
    }
}
