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
namespace Bartlett\Sarif;

use Composer\Autoload\ClassLoader;

use RuntimeException;

use function dirname;
use function file_exists;
use function implode;
use function spl_autoload_register;
use function sprintf;
use function strpos;
use function strtolower;

if (class_exists(__NAMESPACE__ . '\Autoload', false) === false) {
    class Autoload
    {
        public static function load(string $class): void
        {
            static $possibleAutoloadPaths = [];

            $autoloader = '/vendor/autoload.php';

            if (empty($possibleAutoloadPaths)) {
                if (isset($GLOBALS['_composer_autoload_path'])) {
                    $possibleAutoloadPaths[] = $GLOBALS['_composer_autoload_path'];
                } else {
                    foreach ([__DIR__, dirname(__DIR__, 3)] as $path) {
                        $autoloadPath = $path . $autoloader;
                        if (file_exists($autoloadPath)) {
                            $possibleAutoloadPaths[] = $autoloadPath;
                        }
                    }
                }
            }

            $argc ??= $_SERVER['argc'];
            $argv ??= $_SERVER['argv'];

            if ($argc > 2 && 'convert' === $argv[1] && strpos($argv[2], '-h') === false) {
                $source = strtolower($argv[2]);
                $possibleAutoloadPaths[] = __DIR__ . '/vendor-bin/' . $source . $autoloader;
            }

            foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
                if (file_exists($possibleAutoloadPath)) {
                    require $possibleAutoloadPath;
                }
            }

            foreach (ClassLoader::getRegisteredLoaders() as $vendorDir => $loader) {
                $classLoaded = $loader->loadClass($class);

                if ($classLoaded === true) {
                    return;
                }
            }

            throw new RuntimeException(
                sprintf(
                    'Unable to load class "%s" from any autoloader found into "%s" paths.',
                    $class,
                    implode('", "', $possibleAutoloadPaths)
                )
            );
        }
    }

    spl_autoload_register(__NAMESPACE__ . '\Autoload::load', true, true);
}
