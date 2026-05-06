<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter;

use function explode;
use function implode;

/**
 * @author Laurent Laville
 * @since Release 1.6.0
 */
class MagoConverter extends AbstractConverter
{
    public function configure(array $options = []): void
    {
        $this->toolName ??= 'Mago';
        $this->toolShortDescription ??= 'PHP toolchain that analyzes your entire codebase in seconds, not minutes.';
        $this->toolFullDescription ??= 'Mago is a toolchain for PHP that aims to provide a set of tools to help developers write better code';
        $this->toolInformationUri ??= 'https://github.com/carthage-software/mago';
        $this->toolComposerPackage ??= 'carthage-software/mago';

        $options['include_code_snippets'] = false;
        $options['include_context_region'] = false;

        parent::configure($options);

        $versionParts = explode('.', $this->toolSemanticVersion);
        self::$toolVersion = implode('.', [$versionParts[0], $versionParts[1], $versionParts[2]]);
    }
}
