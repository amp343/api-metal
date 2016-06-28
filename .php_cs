<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('.git')
    ->exclude('bin')
    ->exclude('example')
    ->exclude('doc')
    ->exclude('vendor')
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->setUsingCache(true)
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        '-psr0',
        'short_array_syntax',
        'array_element_no_space_before_comma',
        'array_element_white_space_after_comma',
        'double_arrow_multiline_whitespaces',
        'duplicate_semicolon',
        'extra_empty_lines',
        'function_typehint_space',
        'namespace_no_leading_whitespace',
        'no_blank_lines_after_class_opening',
        'operators_spaces',
        'self_accessor',
        'single_blank_line_before_namespace',
        'single_quote',
        'spaces_cast',
        'standardize_not_equal',
        'trim_array_spaces',
        'unused_use',
        'whitespacy_lines',
        'concat_with_spaces',
        'ordered_use'
    ])
    ->finder($finder)
;
