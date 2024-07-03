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
use Bartlett\Sarif\Converter\Source\PhpCsSource;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
class MyReportConverter extends PhpCsReport
{
    public function __construct()
    {
        parent::__construct(
            new PhpCsConverter(null, true),
            PhpCsSource::fromFile(__DIR__ . '/checkstyle.xml', 'checkstyle')
        );
    }
}
