<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Bartlett\Sarif\Converter\PhpStanConverter;
use Bartlett\Sarif\Converter\Reporter\PhpStanFormatter;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class MySarifFormatter extends PhpStanFormatter
{
    public function __construct(bool $prettyPrint)
    {
        parent::__construct(new PhpStanConverter(['format_output' => $prettyPrint]));
    }
}
