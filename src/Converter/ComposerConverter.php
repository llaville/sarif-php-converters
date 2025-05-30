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
 * @since Release 1.2.0
 */
class ComposerConverter extends AbstractConverter
{
    public function configure(array $options = []): void
    {
        $this->toolName ??= 'Composer';
        $this->toolFullName ??= 'ComposerAudit';
        $this->toolShortDescription ??= 'Dependency Manager for PHP.';
        $this->toolFullDescription ??= 'Security audits in Composer 2.4 or greater.';
        $this->toolInformationUri ??= 'https://github.com/composer/composer';
        $this->toolComposerPackage ??= 'composer/composer';

        $options['include_code_snippets'] = false;
        $options['include_context_region'] = false;

        parent::configure($options);
    }
}
