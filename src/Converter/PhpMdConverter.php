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
class PhpMdConverter extends AbstractConverter
{
    public function configure(array $options = []): void
    {
        $this->toolName ??= 'PHPMD';
        $this->toolShortDescription ??= 'Tool with aims to be a PHP equivalent of the well known Java tool PMD';
        $this->toolFullDescription ??= 'PHPMD is a spin-off project of PHP Depend and aims to be a PHP equivalent of the well known Java tool PMD.';
        $this->toolInformationUri ??= 'https://github.com/phpmd/phpmd';
        $this->toolComposerPackage ??= 'phpmd/phpmd';

        parent::configure($options);
    }
}
