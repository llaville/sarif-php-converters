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

use Bartlett\Sarif\Converter\Reporter\RectorFormatter;

use Rector\ChangesReporting\Contract\Output\OutputFormatterInterface;
use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/../../examples',
        __DIR__ . '/../../src',
    ])
    ->withPreparedSets(true)
    ->withRealPathReporting()
    ->withBootstrapFiles([__DIR__ . '/../../vendor/autoload.php'])
    ->registerService(RectorFormatter::class, null, OutputFormatterInterface::class)
;
