<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Contract;

/**
 * @author Laurent Laville
 * @since Release 1.5.0
 */
interface HelpUriCaseConverterInterface
{
    public function convert(string $checkerClass): string;
}
