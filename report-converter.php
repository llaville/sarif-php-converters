<?php declare(strict_types=1);
/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Bartlett\Sarif\Console\Application;
use Bartlett\Sarif\Console\Command\ConvertCommand;

/**
 * A console application tool to convert different error reporting standards.
 *
 * @author Laurent Laville
 * @since Release 1.0.0
 */

$application = new Application();
$application->add(new ConvertCommand());
$application->run();
