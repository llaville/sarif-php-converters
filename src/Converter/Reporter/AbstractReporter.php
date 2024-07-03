<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Reporter;

use Bartlett\Sarif\Contract\ConverterInterface;
use Bartlett\Sarif\Contract\SourceInterface;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
abstract class AbstractReporter
{
    protected ConverterInterface $converter;
    protected SourceInterface $source;

    /**
     * @return iterable<string, string[]>
     */
    protected function getRules(): iterable
    {
        return $this->source->getRules();
    }
}
