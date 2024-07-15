<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Laurent Laville
 * @since Release 1.0.0
 */

return function (): Generator {
    $classes = [
        \Bartlett\Sarif\Converter\TwigCsFixerConverter::class,
        \Bartlett\Sarif\Converter\Source\TwigCsFixerSource::class,
        \Bartlett\Sarif\Converter\Normalizer\TwigCsFixerNormalizer::class,
    ];
    foreach ($classes as $class) {
        yield $class;
    }
};
