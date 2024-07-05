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
use function strpos;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class PhpLintConverter extends AbstractConverter
{
    public function configure(): void
    {
        $this->toolName ??= 'PHPLint';
        $this->toolShortDescription ??= 'Syntax check only (lint) of PHP files';
        $this->toolFullDescription ??= 'PHPLint is a tool that can speed up linting of php files by running several lint processes at once.';
        $this->toolInformationUri ??= 'https://github.com/overtrue/phplint';
        $this->toolComposerPackage ??= 'overtrue/phplint';

        parent::configure();

        $this->toolFullName ??= sprintf(
            '%s version %s by overtrue and contributors',
            $this->toolName,
            $this->toolSemanticVersion ?? '@dev'
        );
    }

    public function invocations(?Definition\PropertyBag $properties = null): array
    {
        $invocations = parent::invocations($properties);

        $arguments = $GLOBALS['argv'];
        $responseFileOption = '--output=';
        foreach ($arguments as $argument) {
            if (strpos($argument, $responseFileOption) === 0) {
                $desc = new Definition\Message();
                $desc->setText('Writing a Report to a File');
                $responseFile = new Definition\ArtifactLocation();
                $responseFile->setDescription($desc);
                $responseFile->setUri($this->pathToUri(str_replace($responseFileOption, '', $argument)));
                $invocations[0]->addResponseFiles([$responseFile]);
            }
        }

        return $invocations;
    }
}
