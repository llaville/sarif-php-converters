<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Bartlett\Sarif\Converter\Source;

use Bartlett\Sarif\Converter\Normalizer\PhpLintNormalizer;

/**
 * @author Laurent Laville
 * @since Release 1.0.0
 */
final class PhpLintSource extends AbstractSource
{
    public function __construct(iterable $normalizers = [])
    {
        if (empty($normalizers)) {
            $normalizers = [new PhpLintNormalizer()];
        }
        parent::__construct($normalizers);
    }
}
