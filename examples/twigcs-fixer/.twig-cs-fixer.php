<?php

require_once dirname(__DIR__, 2) . '/vendor/autoload.php';
require_once __DIR__ . '/MyTwigCsFixerReporter.php';

$ruleset = new TwigCsFixer\Ruleset\Ruleset();

$ruleset->addStandard(new TwigCsFixer\Standard\TwigCsFixer());

$config = new TwigCsFixer\Config\Config();
$config->setRuleset($ruleset);
$config->addCustomReporter(new MyTwigCsFixerReporter());

return $config;
