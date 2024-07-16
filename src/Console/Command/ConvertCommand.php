<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Console\Command;

use Bartlett\Sarif\Contract\NormalizerInterface;
use Bartlett\Sarif\Converter\Normalizer;
use Bartlett\Sarif\Factory\ConverterFactory;
use Bartlett\Sarif\Factory\SourceFactory;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\StreamOutput;

use RuntimeException;
use Throwable;
use function class_exists;
use function fclose;
use function fgets;
use function file_exists;
use function file_get_contents;
use function fopen;
use function realpath;
use function sprintf;
use function stream_select;
use function strtoupper;
use function trim;
use const STDIN;

/**
 * Convert one report third-party tool to SARIF.
 *
 * @author Laurent Laville
 * @since Release 1.0.0
 *
 * @see https://github.com/JBZoo/CI-Report-Converter for some part of code inspired.
 */
class ConvertCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('convert')
            ->setDescription('Convert one report to SARIF')
            ->addArgument(
                'source',
                InputArgument::REQUIRED,
                'Source identifier.'
            )
            ->addArgument(
                'converter',
                InputArgument::OPTIONAL,
                'Converter identifier.'
            )
            ->addOption(
                'bootstrap',
                null,
                InputOption::VALUE_REQUIRED,
                'A PHP script that is included before execution of the ' . $this->getName() . ' command.'
            )
            ->addOption(
                'input-format',
                null,
                InputOption::VALUE_REQUIRED,
                'Source format.',
            )
            ->addOption(
                'input-file',
                null,
                InputOption::VALUE_OPTIONAL,
                'File path with the original report format. If not set or empty, then the STDIN is used.',
            )
            ->addOption(
                'output-file',
                null,
                InputOption::VALUE_OPTIONAL,
                'File path with the result report format. If not set or empty, then the STDOUT is used.',
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = $input->getOptions();
        $bootstrap = $options['bootstrap'] ?? '';

        if (realpath($bootstrap) !== false && file_exists($bootstrap)) {
            require_once $bootstrap;
        }

        try {
            if (empty($options['input-format'])) {
                throw new RuntimeException('Input format is required');
            }
            $format = $options['input-format'];

            $normalizers = [];

            switch ($format) {
                case NormalizerInterface::FORMAT_CHECKSTYLE:
                    $normalizers[] = new Normalizer\CheckstyleNormalizer();
                    break;
                default:
                    // no extra normalizer detected
            }

            $sourceAlias = $input->getArgument('source');

            $sourceFactory = new SourceFactory();
            $source = $sourceFactory->create($sourceAlias, $normalizers);

            $properties = null;
            $rulePrefix = strtoupper($source->getName());
            $context = ['rulePrefix' => $rulePrefix];

            $inputFile = $options['input-file'];
            $inputData = $this->getSourceData($inputFile);

            $isNormalized = $source->normalize($inputData, $format, $context);
            if (!$isNormalized) {
                throw new RuntimeException(
                    sprintf('Unable to convert source "%s" with format "%s"', $inputFile, $format)
                );
            }

            $converterAlias = $input->getArgument('converter') ? : $sourceAlias;

            $converterOptions = [
                // Nicely formats output with indentation and extra space
                'format_output' => $output->isVerbose(),
            ];

            if (class_exists($converterAlias)) {
                // give a chance to use custom converter
                $converter = new $converterAlias($converterOptions);
            } else {
                // use default built-in converters
                $converterFactory = new ConverterFactory();
                $converter = $converterFactory->create($converterAlias, $converterOptions);
            }

            $converter->results($source->getErrors());
            $run = $converter->run($converter->invocations($properties), $source->getRules());
            $jsonString = $converter->sarifLog([$run]);

            if (!empty($jsonString)) {
                if (empty($options['output-file'])) {
                    $output->write($jsonString);
                } else {
                    $outputFile = $options['output-file'];

                    $streamOutput = new StreamOutput(fopen($outputFile, 'w'));
                    $streamOutput->write($jsonString);
                    fclose($streamOutput->getStream());

                    $output->writeln(sprintf('Writing results to <info>%s</info>', $outputFile));
                }
            }
        } catch (Throwable $exception) {
            $output->writeln('<error>' . $exception->getMessage() . '</error>');
            if ($output->isDebug()) {
                $output->writeln($exception->getTraceAsString());
            }
            return $exception->getCode();
        }

        return Command::SUCCESS;
    }

    protected function getSourceData(?string $inputFile): ?string
    {
        if (!empty($inputFile)) {
            if (realpath($inputFile) === false && !file_exists($inputFile)) {
                throw new RuntimeException(
                    sprintf('File "%s" not found', $inputFile),
                    2
                );
            }

            return file_get_contents($inputFile) ? : null;
        }

        $contents = $this->getStdIn();
        if (trim($contents) === '') {
            throw new RuntimeException('Please provide a valid input file or use STDIN as input.');
        }

        return $contents;
    }

    /**
     * Reads input from STDIN with an optional timeout.
     *
     * @param  int $timeout     The timeout value in seconds
     * @return null|string      The string read from STDIN, or null if an error occurred
     * @throws RuntimeException if there was an error reading from STDIN or if the read operation timed out
     */
    protected function getStdIn(int $timeout = 2): ?string
    {
        static $result; // It can be read only once, so we save result as internal variable

        if ($result === null) {
            $result = '';

            $read        = [STDIN];
            $write       = [];
            $except      = [];
            $streamCount = stream_select($read, $write, $except, $timeout);

            if ($streamCount > 0) {
                while ($line = fgets(STDIN, 1024)) {
                    $result .= $line;
                }
            }

            if ($result === '') {
                throw new RuntimeException(
                    sprintf('Reading from STDIN timed out (%d seconds)', $timeout),
                    127
                );
            }
        }

        return $result;
    }
}
