<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter;

use Bartlett\Sarif\Contract\ConverterInterface;
use Bartlett\Sarif\Definition;
use Bartlett\Sarif\Factory;
use Bartlett\Sarif\SarifLog;
use Bartlett\Sarif\Serializer;

use Composer\InstalledVersions;

use PHP_Parallel_Lint\PhpConsoleColor\ConsoleColor;
use PHP_Parallel_Lint\PhpConsoleHighlighter\Highlighter;

use Iterator;
use RuntimeException;
use Throwable;
use function array_intersect;
use function array_map;
use function array_shift;
use function array_slice;
use function array_unshift;
use function count;
use function date;
use function end;
use function explode;
use function file_get_contents;
use function get_class;
use function getcwd;
use function gmdate;
use function implode;
use function is_array;
use function parse_url;
use function rtrim;
use function sprintf;
use function str_replace;
use function strlen;
use function strpos;
use function substr;
use function trim;
use const DATE_ATOM;
use const DIRECTORY_SEPARATOR;
use const JSON_PRETTY_PRINT;
use const JSON_UNESCAPED_SLASHES;
use const PHP_URL_SCHEME;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
abstract class AbstractConverter implements ConverterInterface
{
    protected string $toolName;
    protected ?string $toolFullName = null;
    protected string $toolShortDescription;
    protected string $toolFullDescription;
    protected string $toolInformationUri;
    protected string $toolComposerPackage;
    protected string $toolSemanticVersion;
    protected string $converterComposerPackage;

    /**
     * @var Definition\Result[]
     */
    protected array $results = [];

    /**
     * @var array<string, array<int, mixed>> $errors List of error by file
     */
    protected array $errors = [];

    protected Serializer\SerializerInterface $serializer;

    /**
     * Specify the UTC date and time at which the invocation started.
     */
    protected ?int $startTime = null;

    /**
     * Specify the UTC date and time at which the invocation ended.
     */
    protected ?int $endTime = null;

    protected bool $includeCodeSnippets;
    protected bool $includeContextRegion;

    /**
     * @param array{
     *     format_output?: bool,
     *     include_code_snippets?: bool,
     *     include_context_region?: bool
     * } $options
     */
    public function __construct(array $options = [], ?Factory\SerializerFactory $factory = null)
    {
        $prettyPrint = $options['format_output'] ?? false;  // default machine-readable format (compact)
        $serializerFactory = new Factory\PhpSerializerFactory();

        if ($prettyPrint) {
            $realEncoder = new Serializer\Encoder\PhpJsonEncoder(JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $this->serializer = $serializerFactory->createSerializer($realEncoder);
        } else {
            $factory ??= $serializerFactory;
            $this->serializer = $factory->createSerializer();
        }

        $options['format_output'] = $prettyPrint;
        $this->configure($options);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        $names = explode("\\", get_class($this));
        return rtrim(end($names), 'Converter');
    }

    /**
     * @param array{
     *     format_output?: bool,
     *     include_code_snippets?: bool,
     *     include_context_region?: bool
     * } $options
     */
    public function configure(array $options): void
    {
        $this->converterComposerPackage = 'bartlett/sarif-php-converters';
        if (!empty($this->toolComposerPackage)) {
            $this->toolSemanticVersion = $this->getToolVersion($this->toolComposerPackage);
        }

        // set option to avoid additional suggestion SARIF2010
        $this->includeCodeSnippets = $options['include_code_snippets'] ?? true;

        // set option to avoid additional suggestion SARIF2011
        $this->includeContextRegion = $options['include_context_region'] ?? true;
    }

    public function toolDriver(): Definition\ToolComponent
    {
        $driver = new Definition\ToolComponent();
        $driver->setName($this->toolName);
        $driver->setInformationUri($this->toolInformationUri);
        if (!empty($this->toolSemanticVersion)) {
            $driver->setSemanticVersion($this->toolSemanticVersion);
        }

        if (!empty($this->toolFullName)) {
            $driver->setFullName($this->toolFullName);
        }
        if (!empty($this->toolShortDescription)) {
            $description = new Definition\MultiformatMessageString();
            $description->setText($this->toolShortDescription);
            $driver->setShortDescription($description);
        }
        if (!empty($this->toolFullDescription)) {
            $description = new Definition\MultiformatMessageString();
            $description->setText($this->toolFullDescription);
            $driver->setFullDescription($description);
        }

        return $driver;
    }

    /**
     * @inheritDoc
     */
    public function toolExtensions(): array
    {
        $converterVersion = InstalledVersions::getVersion($this->converterComposerPackage);

        $description = new Definition\MultiformatMessageString();
        $description->setText($this->toolName . ' SARIF Converter');

        $extension = new Definition\ToolComponent();
        $extension->setName($this->converterComposerPackage);
        $extension->setShortDescription($description);
        $extension->setVersion($converterVersion);

        return [$extension];
    }

    /**
     * @inheritDoc
     */
    final public function rules(iterable $defined): array
    {
        $rules = [];

        foreach ($defined as $ruleId => $ruleDef) {
            $rule = new Definition\ReportingDescriptor();
            $rule->setId($ruleId);
            if (!empty($ruleDef['name'])) {
                $rule->setName($ruleDef['name']);
            }
            if (!empty($ruleDef['shortDescription'])) {
                $description = new Definition\MultiformatMessageString();
                $description->setText($ruleDef['shortDescription']);
                $rule->setShortDescription($description);
            }
            if (!empty($ruleDef['fullDescription'])) {
                $description = new Definition\MultiformatMessageString();
                $description->setText($ruleDef['fullDescription']);
                $rule->setFullDescription($description);
            }
            if (!empty($ruleDef['messageStrings'])) {
                foreach ($ruleDef['messageStrings'] as $key => $value) {
                    $messageString = new Definition\MultiformatMessageString();
                    $messageString->setText($value);
                    $rule->addMessageStrings([$key => $messageString]);
                }
            }
            if (!empty($ruleDef['help'])) {
                $markdown = '';
                if (is_array($ruleDef['help'])) {
                    $text = $ruleDef['help']['text'];
                    $markdown = $ruleDef['help']['markdown'] ?? $markdown;
                } else {
                    $text = $ruleDef['help'];
                }
                $help = new Definition\MultiformatMessageString();
                $help->setText($text);
                if (!empty($markdown)) {
                    $help->setMarkdown($markdown);
                }
                $rule->setHelp($help);
            }
            if (!empty($ruleDef['helpUri'])) {
                $rule->setHelpUri($ruleDef['helpUri']);
            }
            if (!empty($ruleDef['properties'])) {
                $properties = new Definition\PropertyBag();
                $properties->addProperties($ruleDef['properties']);
                $rule->setProperties($properties);
            }

            $rules[] = $rule;
        }

        return $rules;
    }

    final public function results(iterable $normalized): void
    {
        foreach ($normalized as $filename => $errors) {
            $artifactLocation = new Definition\ArtifactLocation();
            $artifactLocation->setUri($this->pathToArtifactLocation($filename));
            $artifactLocation->setUriBaseId('WORKINGDIR');

            $fingerprint = hash_file('sha256', $filename);
            $surroundingLines = 2;

            foreach ($errors as $error) {
                $ruleId = $error['ReportingDescriptor.id'] ?? '';

                $fingerprintId = $error['Result.fingerprint'] ?? $ruleId;

                $text = $error['Result.message'] ?? '';
                $message = new Definition\Message();
                $message->setText($text);
                if (!empty($error['Result.message.id'])) {
                    $message->setId($error['Result.message.id']);
                }

                if (!empty($error['Result.message.arguments'])) {
                    $message->addArguments(array_map('strval', $error['Result.message.arguments']));
                }

                $result = new Definition\Result();
                $result->setMessage($message);
                $result->addPartialFingerprints([$fingerprintId => $fingerprint]);
                $result->setRuleId($ruleId);

                if (!empty($error['Result.level'])) {
                    $result->setLevel($error['Result.level']);
                }

                $physicalLocation = new Definition\PhysicalLocation();
                $physicalLocation->setArtifactLocation($artifactLocation);

                if (!empty($error['Region.startLine'])) {
                    $line = (int) $error['Region.startLine'];
                    $endLine = $error['Region.endLine'] ?? null;
                    $column = $error['Region.startColumn'] ?? null;
                    if (!empty($column)) {
                        $column = (int) $column;
                    }
                    $region = $this->getSnippetRegion($filename, $line, $column, 0, 0);
                    if (!empty($endLine)) {
                        $region->setEndLine((int) $endLine);
                    }
                    $physicalLocation->setRegion($region);

                    if ($this->includeContextRegion) {
                        $startLine = max($line - $surroundingLines, 1);
                        $contextRegion = $this->getSnippetRegion(
                            $filename,
                            $startLine,
                            null,
                            0,
                            $surroundingLines * 2
                        );
                        $contextRegion->setEndLine($line + $surroundingLines);
                        $physicalLocation->setContextRegion($contextRegion);
                    }
                }

                $location = new Definition\Location();
                $location->setPhysicalLocation($physicalLocation);

                $result->addLocations([$location]);

                $properties = [];
                foreach ($error as $key => $value) {
                    if (strpos($key, 'Result.properties.') !== false) {
                        $properties[str_replace('Result.properties.', '', $key)] = $value;
                    }
                }
                if (!empty($properties)) {
                    $resultProperties = new Definition\PropertyBag();
                    $resultProperties->addProperties($properties);
                    $result->setProperties($resultProperties);
                }

                $this->results[] = $result;
            }
        }
    }

    /**
     * @inheritDoc
     */
    final public function sarifLog(array $runs = [], string $version = '2.1.0'): ?string
    {
        $log = new SarifLog($runs, $version);

        return $this->serializer->serialize($log, 'json');
    }

    /**
     * @inheritDoc
     */
    public function invocations(?Definition\PropertyBag $properties = null): array
    {
        $workingDir = new Definition\ArtifactLocation();
        $workingDir->setUri($this->pathToUri(getcwd() . '/'));

        $invocation = new Definition\Invocation();
        $invocation->setExecutionSuccessful(true);
        $invocation->setWorkingDirectory($workingDir);

        $arguments = $GLOBALS['argv'];
        $cmd = array_shift($arguments);
        $invocation->setCommandLine($cmd);
        $invocation->addArguments($arguments);

        $utcFormat = "Y-m-d\TH:i:s\Z";
        if ($this->startTime) {
            $invocation->setStartTimeUtc(gmdate($utcFormat, $this->startTime));
        }
        if ($this->endTime) {
            $invocation->setEndTimeUtc(gmdate($utcFormat, $this->endTime));
        }

        if ($properties !== null) {
            $invocation->setProperties($properties);
        }

        return [$invocation];
    }

    public function automationDetails(): Definition\RunAutomationDetails
    {
        $automationDetails = new Definition\RunAutomationDetails();
        $automationDetails->setId('Daily run ' . date(DATE_ATOM));

        return $automationDetails;
    }

    final public function run(iterable $invocations, iterable $rules): Definition\Run
    {
        $driver = $this->toolDriver();
        $driver->addRules($this->rules($rules));

        $tool = new Definition\Tool();
        $tool->setDriver($driver);
        $tool->addExtensions($this->toolExtensions());

        $run = new Definition\Run();
        $run->setTool($tool);

        $run->addInvocations((array) $invocations);
        $run->addResults($this->results);
        $run->setAutomationDetails($this->automationDetails());

        $workingDir = new Definition\ArtifactLocation();
        $workingDir->setUri($this->pathToUri(getcwd() . '/'));
        $originalUriBaseIds = [
            'WORKINGDIR' => $workingDir,
        ];
        $run->addAdditionalProperties($originalUriBaseIds);

        return $run;
    }

    protected function getToolVersion(string $package): string
    {
        $toolVersion = InstalledVersions::getVersion($package);

        if (null === $toolVersion) {
            throw new RuntimeException(
                sprintf('%s requires to have %s installed (package "%s").', static::class, $this->toolName, $package)
            );
        }

        return $toolVersion;
    }

    /**
     * Returns path to resource (file) scanned.
     */
    protected function pathToArtifactLocation(string $path): string
    {
        $workingDir = getcwd();
        if ($workingDir === false) {
            $workingDir = '.';
        }
        if (substr($path, 0, strlen($workingDir)) === $workingDir) {
            // have common path
            return substr($path, strlen($workingDir) + 1);
        }

        // make $path relative to working directory
        $cwd = explode('/', trim($workingDir, '/'));
        $rPath = explode('/', trim($path, '/'));
        $commonParts = array_intersect($cwd, $rPath);

        $relativeCwd = array_slice($cwd, count($commonParts));
        $relativePath = array_slice($rPath, count($commonParts));

        foreach ($relativeCwd as $item) {
            array_unshift($relativePath, '..');
        }
        return implode('/', $relativePath);
    }

    /**
     * Returns path to resource (file) scanned with protocol.
     */
    protected function pathToUri(string $path): string
    {
        if (parse_url($path, PHP_URL_SCHEME) !== null) {
            // already a URL
            return $path;
        }

        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);

        // file:///C:/... on Windows systems
        if (substr($path, 0, 1) !== '/') {
            $path = '/' . $path;
        }

        return 'file://' . $path;
    }

    /**
     * Provides code snippet.
     *
     * Only if `php-parallel-lint/php-console-highlighter` package is installed
     * @link https://packagist.org/packages/php-parallel-lint/php-console-highlighter
     */
    protected function getCodeSnippet(string $filePath, int $lineNumber, int $linesBefore, int $linesAfter): ?string
    {
        if (InstalledVersions::isInstalled('php-parallel-lint/php-console-highlighter') === false) {
            return null;
        }

        try {
            $highlighter = new Highlighter(new ConsoleColor());
            $fileContent = file_get_contents(\realpath($filePath));
            $snippet = $highlighter->getCodeSnippet($fileContent, $lineNumber, $linesBefore, $linesAfter);
        } catch (Throwable $exception) {
            $snippet = null;
        }
        return $snippet;
    }

    protected function getSnippetRegion(
        string $filePath,
        int $lineNumber,
        ?int $column = null,
        int $linesBefore = 2,
        int $linesAfter = 2
    ): Definition\Region {
        $region = new Definition\Region();
        $region->setStartLine($lineNumber);
        if (null !== $column) {
            $region->setStartColumn($column);
        }

        if ($this->includeCodeSnippets) {
            $snippet = $this->getCodeSnippet($filePath, $lineNumber, $linesBefore, $linesAfter);
            if ($snippet !== null) {
                $rendered = new Definition\MultiformatMessageString();
                $rendered->setText($snippet);
                $artifactContent = new Definition\ArtifactContent();
                $artifactContent->setRendered($rendered);
                $region->setSnippet($artifactContent);
            }
        }

        return $region;
    }

    protected function getErrors(): Iterator
    {
        foreach ($this->errors as $filename => $errors) {
            yield $filename => $errors;
        }
    }
}
