<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Bartlett\Sarif\Converter\RectorConverter;
use Bartlett\Sarif\Converter\Reporter\RectorFormatter;

/**
 * @author Laurent Laville
 * @since Release 1.1.0
 */
class MySarifFormatter extends RectorFormatter
{
    public function __construct()
    {
        parent::__construct(
            new RectorConverter(
                [
                    'format_output' => true,
                ]
            )
        );
    }
}
