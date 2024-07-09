<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MyStandard\CS;

use Bartlett\Sarif\Converter\PhpCsConverter;
use Bartlett\Sarif\Converter\Reporter\PhpCsReport;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class MyPhpCsReport extends PhpCsReport
{
    public function __construct()
    {
        // enable pretty print flag
        parent::__construct(new PhpCsConverter(null, true));
    }

    protected function getRules(): iterable
    {
        foreach (parent::getRules() as $ruleId => $ruleDef) {
            /**
             * Here we want list of PHP CS rules but not the frequency calls (provided on default behavior)
             */
            $newRuleDef = $ruleDef;
            unset($newRuleDef['properties']['frequency']);
            yield $ruleId => $newRuleDef;
        }
    }
}
