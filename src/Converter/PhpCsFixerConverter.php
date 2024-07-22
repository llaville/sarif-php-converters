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
class PhpCsFixerConverter extends AbstractConverter
{
    public function configure(array $options): void
    {
        $this->toolName ??= 'PHP-CS-Fixer';
        $this->toolFullDescription ??= 'A tool to automatically fix PHP Coding Standards issues.';
        $this->toolInformationUri ??= 'https://github.com/PHP-CS-Fixer/PHP-CS-Fixer';
        $this->toolComposerPackage ??= 'friendsofphp/php-cs-fixer';

        parent::configure($options);
    }
}
