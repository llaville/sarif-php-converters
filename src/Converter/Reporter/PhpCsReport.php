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
use Bartlett\Sarif\Converter\PhpCsConverter;
use Bartlett\Sarif\Converter\Source\PhpCsSource;
use Bartlett\Sarif\Definition\PropertyBag;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Reports\Report;

use function explode;
use function json_encode;
use function sprintf;
use function strtolower;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class PhpCsReport extends AbstractReporter implements Report
{
    public const URI_PATTERN = 'https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki/Customisable-Sniff-Properties#%s';

    /**
     * @var array<string, array<string, int>|array<string>> $cacheData
     */
    private array $cacheData;

    public function __construct(?ConverterInterface $converter = null, ?SourceInterface $source = null)
    {
        $this->converter = $converter ?? new PhpCsConverter();
        $this->source = $source ?? new PhpCsSource();
        $this->cacheData = [
            'totals' => [
                'errors' => 0,
                'warnings' => 0,
                'fixable' => 0,
            ],
            'files' => [],
        ];
    }

    protected function getRules(): iterable
    {
        foreach (parent::getRules() as $ruleId => $ruleDef) {
            /**
             * Dynamically add helpUri on each rule (Sniff), if not already defined
             */
            if (isset($ruleDef['helpUri'])) {
                continue;
            }

            // Rule ID format :(standard).(group).(name).(code)
            // e.g: PSR12.Operators.OperatorSpacing.NoSpaceBefore
            $nameParts = explode('.', $ruleId);

            $standard = $nameParts[0];
            $group = $nameParts[1];
            $name = $nameParts[2];

            $helpUri = sprintf(self::URI_PATTERN, strtolower($standard . $group . $name));

            $ruleDef['helpUri'] = $helpUri;
            yield $ruleId => $ruleDef;
        }
    }

    /**
     * @param array<string, mixed> $report
     */
    public function generateFileReport($report, File $phpcsFile, $showSources = false, $width = 80): bool
    {
        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            // Nothing to print.
            return false;
        }

        $this->cacheData['totals']['errors'] += $report['errors'];
        $this->cacheData['totals']['warnings'] += $report['warnings'];
        $this->cacheData['totals']['fixable'] += $report['fixable'];

        $messages = [];

        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $error['line'] = $line;
                    $error['column'] = $column;
                    $error['type'] = strtolower($error['type']);

                    $messages[] = $error;
                }
            }
        }

        $this->cacheData['files'][$report['filename']] = [
            'errors' => $report['errors'],
            'warnings' => $report['warnings'],
            'messages' => $messages,
        ];

        return true;
    }

    public function generate(
        $cachedData,
        $totalFiles,
        $totalErrors,
        $totalWarnings,
        $totalFixable,
        $showSources = false,
        $width = 80,
        $interactive = false,
        $toScreen = true
    ) {
        $this->source->normalize(json_encode($this->cacheData), 'json', []);

        $this->converter->results($this->source->getErrors());

        $properties = new PropertyBag();
        $properties->addProperties([
            'totals.files' => $totalFiles,
            'totals.errors' => $totalErrors,
            'totals.warnings' => $totalWarnings,
            'totals.fixable' => $totalFixable,
        ]);

        $run = $this->converter->run($this->converter->invocations($properties), $this->getRules());

        echo $this->converter->sarifLog([$run]);
    }
}
