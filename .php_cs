<?php

$excluded_folders = [
    'node_modules',
    'storage',
    'vendor',
    'bootstrap',
    'config'
];
$finder = PhpCsFixer\Finder::create()
    ->exclude($excluded_folders)
    ->notName('AcceptanceTester.php')
    ->notName('FunctionalTester.php')
    ->notName('UnitTester.php')
    ->notName('README.md')
    ->notName('*.xml')
    ->notName('*.yml');
;

$rules = [
    '@PSR2' => true,
    // addtional rules
    'align_multiline_comment' => true,
    'array_syntax' => ['syntax' => 'short'],
    'blank_line_after_namespace' => true,
    'no_multiline_whitespace_before_semicolons' => true,
    'no_short_echo_tag' => true,
    'no_unused_imports' => true,
    'not_operator_with_successor_space' => true,
];

return PhpCsFixer\Config::create()
    ->setRules($rules)
    ->setFinder($finder)

?>