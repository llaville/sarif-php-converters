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

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class PhpStanConverter extends AbstractConverter
{
    public function configure(array $options = []): void
    {
        $this->toolName ??= 'PHPStan';
        $this->toolShortDescription ??= 'PHP Static Analysis Tool';
        $this->toolFullDescription ??= 'PHPStan - PHP Static Analysis Tool';
        $this->toolInformationUri ??= 'https://phpstan.org';

        try {
            // try with mapped package
            // @see https://getcomposer.org/doc/04-schema.md#replace
            $this->toolComposerPackage = 'phpstan/phpstan';
            $this->toolSemanticVersion = $this->getToolVersion($this->toolComposerPackage);
        } catch (\Throwable $exception) {
            $this->toolComposerPackage = 'phpstan/phpstan-src';
        }
        parent::configure($options);
    }

    public function toolDriver(): Definition\ToolComponent
    {
        $this->toolFullName ??= sprintf(
            '%s %s',
            $this->toolFullDescription,
            $this->toolSemanticVersion
        );
        return parent::toolDriver();
    }
}
