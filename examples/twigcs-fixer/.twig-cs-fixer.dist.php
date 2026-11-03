<?php

/**
 * This file is part of the Sarif-PHP-Converters package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

$ruleset = new TwigCsFixer\Ruleset\Ruleset();

$ruleset->addStandard(new TwigCsFixer\Standard\TwigCsFixer());

$config = new TwigCsFixer\Config\Config();
$config->setRuleset($ruleset);
// prettyPrint argument is allowed since release 1.9.0
$config->addCustomReporter(new Bartlett\Sarif\Converter\Reporter\TwigCsFixerReporter(prettyPrint: false));

return $config;
