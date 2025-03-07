<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter;

use Bartlett\Sarif\Definition;

use function sprintf;
use function str_replace;
use function str_starts_with;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class PhpCsConverter extends AbstractConverter
{
    public function configure(array $options = []): void
    {
        $this->toolName ??= 'PHP_CodeSniffer';
        $this->toolShortDescription ??= 'Detects violations of a defined set of coding standards.';
        $this->toolFullDescription ??= 'PHP_CodeSniffer tokenizes PHP, JavaScript and CSS files and detects violations of a defined set of coding standards.';
        $this->toolInformationUri ??= 'https://github.com/squizlabs/PHP_CodeSniffer';
        $this->toolComposerPackage ??= 'squizlabs/php_codesniffer';

        parent::configure($options);

        $this->toolFullName ??= sprintf(
            '%s version %s by Squiz and PHPCSStandards',
            $this->toolName,
            $this->toolSemanticVersion
        );
    }

    public function invocations(?Definition\PropertyBag $properties = null): array
    {
        $invocations = parent::invocations($properties);

        $arguments = $GLOBALS['argv'];
        $responseFileOption = '--report-file=';
        foreach ($arguments as $argument) {
            if (str_starts_with($argument, $responseFileOption)) {
                $desc = new Definition\Message();
                $desc->setText('Writing a Report to a File');
                // @link https://github.com/PHPCSStandards/PHP_CodeSniffer/wiki/Reporting#writing-a-report-to-a-file
                $responseFile = new Definition\ArtifactLocation();
                $responseFile->setDescription($desc);
                $responseFile->setUri($this->pathToUri(str_replace($responseFileOption, '', $argument)));
                $invocations[0]->addResponseFiles([$responseFile]);
            }
        }

        return $invocations;
    }
}
