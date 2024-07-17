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
class PhanConverter extends AbstractConverter
{
    public function configure(array $options = []): void
    {
        $this->toolName ??= 'Phan';
        $this->toolShortDescription ??= 'Phan is a static analyzer for PHP';
        $this->toolFullDescription ??= 'Phan prefers to avoid false-positives and attempts to prove incorrectness rather than correctness.';
        $this->toolInformationUri ??= 'https://github.com/phan/phan';
        $this->toolComposerPackage ??= 'phan/phan';

        parent::configure($options);
    }
}
