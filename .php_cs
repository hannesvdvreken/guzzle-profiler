<?php

$finder = PhpCsFixer\Finder::create()->in(['src', 'tests']);

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        '@PhpCsFixer' => true,
        'ereg_to_preg' => true,
        'no_php4_constructor' => true,
        'strict_comparison' => true,
        'strict_param' => true,
    ])
    ->setFinder($finder);
