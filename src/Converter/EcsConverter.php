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
class EcsConverter extends AbstractConverter
{
    public function configure(): void
    {
        $this->toolName ??= 'Easy-Coding-Standard';
        $this->toolShortDescription ??= 'The Easiest way to use Coding Standard.';
        $this->toolFullDescription ??= 'Use Coding Standard with 0-knowledge of PHP-CS-Fixer and PHP_CodeSniffer.';
        $this->toolInformationUri ??= 'https://github.com/easy-coding-standard/easy-coding-standard';
        $this->toolComposerPackage ??= 'symplify/easy-coding-standard';

        parent::configure();
    }
}
