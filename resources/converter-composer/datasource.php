<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Laurent Laville
 * @since Release 1.2.0
 */

return function (): Generator {
    $classes = [
        \Bartlett\Sarif\Converter\ComposerConverter::class,
        \Bartlett\Sarif\Converter\Source\ComposerSource::class,
        \Bartlett\Sarif\Converter\Normalizer\ComposerNormalizer::class,
    ];
    foreach ($classes as $class) {
        yield $class;
    }
};
