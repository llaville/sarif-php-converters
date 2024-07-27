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
use Bartlett\Sarif\Converter\RectorConverter;
use Bartlett\Sarif\Converter\Source\RectorSource;
use Bartlett\Sarif\Definition\PropertyBag;

use Rector\ChangesReporting\Contract\Output\OutputFormatterInterface;
use Rector\ValueObject\Configuration;
use Rector\ValueObject\ProcessResult;

use function count;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class RectorFormatter extends AbstractReporter implements OutputFormatterInterface
{
    protected string $rulePrefix;

    public function __construct(?ConverterInterface $converter = null, ?SourceInterface $source = null)
    {
        $this->converter = $converter ?? new RectorConverter();
        $this->source = $source ?? new RectorSource();
        $this->rulePrefix = 'RECTOR';
    }

    public function getName(): string
    {
        return 'sarif';
    }

    public function report(ProcessResult $processResult, Configuration $configuration): void
    {
        $this->source->normalize($processResult, 'internal', ['rulePrefix' => $this->rulePrefix]);

        $this->converter->results($this->source->getErrors());

        $properties = new PropertyBag();
        $properties->addProperties([
            'changed_files' => count($processResult->getFileDiffs()),
        ]);

        $run = $this->converter->run($this->converter->invocations($properties), $this->getRules());

        echo $this->converter->sarifLog([$run]);
    }
}
