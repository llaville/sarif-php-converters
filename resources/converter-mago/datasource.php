<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Laurent Laville
 * @since Release 1.6.0
 */

use Bartlett\Sarif\Converter\Case\MagoHelpUriCaseConverter;
use Bartlett\Sarif\Converter\MagoConverter;
use Bartlett\Sarif\Converter\Normalizer\MagoNormalizer;
use Bartlett\Sarif\Converter\Source\MagoSource;

return function (): Generator {
    $classes = [
        MagoConverter::class,
        MagoSource::class,
        MagoNormalizer::class,
        MagoHelpUriCaseConverter::class,
    ];
    foreach ($classes as $class) {
        yield $class;
    }
};
