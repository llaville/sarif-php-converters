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
class PsalmConverter extends AbstractConverter
{
    public function configure(array $options = []): void
    {
        $this->toolName ??= 'Psalm';
        $this->toolShortDescription ??= 'Find errors in codebase';
        $this->toolFullDescription ??= 'A static analysis tool for finding errors in PHP applications';
        $this->toolInformationUri ??= 'https://github.com/vimeo/psalm';
        $this->toolComposerPackage ??= 'vimeo/psalm';

        parent::configure($options);
    }
}
