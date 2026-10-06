<?php

declare(strict_types=1);

/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Bartlett\Sarif\Converter\Reporter;

use Bartlett\Sarif\Contract\ConverterInterface;
use Bartlett\Sarif\Contract\SourceInterface;
use Bartlett\Sarif\Converter\PhpMdConverter;
use Bartlett\Sarif\Converter\Source\PhpMdSource;

use PHPMD\AbstractRenderer;
use PHPMD\Renderer\Option\Verbose;
use PHPMD\Report;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class created to boot external resource following implementation :
 * @link https://github.com/phpmd/phpmd/pull/1197
 *
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class PhpMdRenderer extends AbstractRenderer implements Verbose
{
    protected ConverterInterface $converter;
    protected SourceInterface $source;
    protected int $verbosityLevel;

    public function __construct(?SourceInterface $source = null)
    {
        $this->source = $source ?? new PhpMdSource();
    }

    public function setVerbosityLevel(int $level): void
    {
        $this->verbosityLevel = $level;

        $prettyPrint = $level > OutputInterface::VERBOSITY_NORMAL;
        $this->converter = new PhpMdConverter(['format_output' => $prettyPrint]);
    }

    /**
     * @inheritDoc
     */
    public function renderReport(Report $report): void
    {
        $this->source->normalize($report, 'internal', []);

        $this->converter->results($this->source->getErrors());

        $run = $this->converter->run($this->converter->invocations(), $this->getRules());

        $jsonString = $this->converter->sarifLog([$run]);

        $writer = $this->getWriter();
        $writer->write($jsonString);
    }

    /**
     * @return iterable<string, string[]>
     */
    protected function getRules(): iterable
    {
        return $this->source->getRules();
    }
}
