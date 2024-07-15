<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class TwigCsFixerConverter extends AbstractConverter
{
    public function configure(array $options = []): void
    {
        $this->toolName ??= 'Twig-CS-Fixer';
        $this->toolShortDescription ??= 'A tool to automatically fix Twig code style';
        $this->toolFullDescription ??= 'A tool to automatically fix Twig Coding Standards issues.';
        $this->toolInformationUri ??= 'https://github.com/VincentLanglet/Twig-CS-Fixer';
        $this->toolComposerPackage ??= 'vincentlanglet/twig-cs-fixer';

        parent::configure($options);
    }
}
