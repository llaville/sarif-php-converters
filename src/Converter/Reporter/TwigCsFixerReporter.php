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
use Bartlett\Sarif\Converter\Source\TwigCsFixerSource;
use Bartlett\Sarif\Converter\TwigCsFixerConverter;

use Symfony\Component\Console\Output\OutputInterface;

use TwigCsFixer\Report\Report;
use TwigCsFixer\Report\Reporter\ReporterInterface;

use function count;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class TwigCsFixerReporter extends AbstractReporter implements ReporterInterface
{
    private string $rulePrefix;

    public function __construct(?ConverterInterface $converter = null, ?SourceInterface $source = null)
    {
        $this->converter = $converter ?? new TwigCsFixerConverter();
        $this->source = $source ?? new TwigCsFixerSource();

        $this->rulePrefix = 'TWIG_CS_FIXER';
    }

    public function display(
        OutputInterface $output,
        Report $report,
        ?string $level,
        bool $debug
    ): void {
        $data = [];

        foreach ($report->getFiles() as $file) {
            $fileViolations = $report->getFileViolations($file, $level);
            if (0 === count($fileViolations)) {
                continue;
            }
            $realPath = $report->getRealPath($file);
            $data[$realPath] = $fileViolations;
        }

        $this->source->normalize($data, 'internal', ['rulePrefix' => $this->rulePrefix]);

        $this->converter->results($this->source->getErrors());

        $run = $this->converter->run($this->converter->invocations(), $this->getRules());

        echo $this->converter->sarifLog([$run]);
    }

    public function getName(): string
    {
        return 'sarif';
    }
}
