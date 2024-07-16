<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Bartlett\Sarif\Converter\Reporter\TwigCsFixerReporter;
use Bartlett\Sarif\Converter\TwigCsFixerConverter;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class MyTwigCsFixerReporter extends TwigCsFixerReporter
{
    public function __construct()
    {
        // enable pretty print feature
        parent::__construct(
            new TwigCsFixerConverter([
                'format_output' => true,
                'include_code_snippets' => false,
            ])
        );
    }
}
