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
use Bartlett\Sarif\Converter\PhpStanConverter;
use Bartlett\Sarif\Converter\Source\PhpStanSource;

use PHPStan\Command;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class PhpStanFormatter extends AbstractReporter implements Command\ErrorFormatter\ErrorFormatter
{
    protected string $rulePrefix;

    public function __construct(?ConverterInterface $converter = null, ?SourceInterface $source = null)
    {
        $this->converter = $converter ?? new PhpStanConverter();
        $this->source = $source ?? new PhpStanSource();
        $this->rulePrefix = 'PHPSTAN';
    }

    public function formatErrors(Command\AnalysisResult $analysisResult, Command\Output $output): int
    {
        $this->source->normalize($analysisResult, 'internal', ['rulePrefix' => $this->rulePrefix]);

        $this->converter->results($this->source->getErrors());

        $run = $this->converter->run($this->converter->invocations(), $this->getRules());

        $jsonString = $this->converter->sarifLog([$run]);

        $output->writeLineFormatted($jsonString);

        return $analysisResult->hasErrors() ? 1 : 0;
    }
}
