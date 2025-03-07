<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter;

use Throwable;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class RectorConverter extends AbstractConverter
{
    public function configure(array $options): void
    {
        $this->toolName ??= 'Rector';
        $this->toolShortDescription ??= 'PHP Refactoring code';
        $this->toolFullDescription ??= 'Instant Upgrade and Automated Refactoring of any PHP code';
        $this->toolInformationUri ??= 'https://getrector.com';

        try {
            // try with mapped package
            // @see https://getcomposer.org/doc/04-schema.md#replace
            $this->toolComposerPackage = 'rector/rector';
            $this->toolSemanticVersion = $this->getToolVersion($this->toolComposerPackage);
        } catch (Throwable) {
            $this->toolComposerPackage = 'rector/rector-src';
        }
        parent::configure($options);
    }
}
