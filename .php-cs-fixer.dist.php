<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;


$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
    ->exclude('var')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@PSR12' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short']
    ])
    ->setFinder($finder)
;
