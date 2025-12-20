<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . '/app')
    ->in(__DIR__ . '/config')
    ->in(__DIR__ . '/migrations')
    ->in(__DIR__ . '/test')
    ->exclude('runtime')
    ->exclude('vendor')
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@Symfony' => true,
        '@PhpCsFixer' => true,
        '@PHP84Migration' => true,

        // Array
        'array_syntax' => ['syntax' => 'short'],
        'trailing_comma_in_multiline' => [
            'elements' => ['arrays', 'arguments', 'parameters', 'match'],
        ],

        // Imports
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],
        'no_unused_imports' => true,
        'no_leading_import_slash' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => false,
        ],

        // Spacing
        'binary_operator_spaces' => ['default' => 'single_space'],
        'concat_space' => ['spacing' => 'one'],
        'cast_spaces' => ['space' => 'single'],
        'unary_operator_spaces' => true,
        'not_operator_with_successor_space' => false,
        'not_operator_with_space' => false,

        // Statements
        'blank_line_before_statement' => [
            'statements' => ['return', 'try', 'throw', 'declare'],
        ],
        'no_extra_blank_lines' => [
            'tokens' => ['extra', 'throw', 'use', 'curly_brace_block'],
        ],

        // Classes
        'class_attributes_separation' => [
            'elements' => [
                'method' => 'one',
                'property' => 'one',
                'trait_import' => 'none',
            ],
        ],
        'no_blank_lines_after_class_opening' => true,
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'case',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct',
                'destruct',
                'magic',
                'phpunit',
                'method_public',
                'method_protected',
                'method_private',
            ],
        ],
        'single_trait_insert_per_statement' => true,
        'visibility_required' => [
            'elements' => ['method', 'property', 'const'],
        ],

        // Methods
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
        ],
        'return_type_declaration' => ['space_before' => 'none'],

        // PHPDoc
        'phpdoc_align' => ['align' => 'left'],
        'phpdoc_indent' => true,
        'phpdoc_no_access' => true,
        'phpdoc_no_package' => true,
        'phpdoc_order' => true,
        'phpdoc_separation' => true,
        'phpdoc_summary' => true,
        'phpdoc_trim' => true,
        'phpdoc_types' => true,
        'phpdoc_var_annotation_correct_order' => true,
        'phpdoc_scalar' => true,
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name' => true,
        'no_blank_lines_after_phpdoc' => true,
        'no_superfluous_phpdoc_tags' => [
            'allow_mixed' => true,
            'remove_inheritdoc' => false,
        ],

        // Other
        'declare_strict_types' => true,
        'single_quote' => true,
        'lowercase_cast' => true,
        'short_scalar_cast' => true,
        'no_trailing_comma_in_singleline' => true,
        'combine_consecutive_unsets' => true,
        'linebreak_after_opening_tag' => true,
        'lowercase_static_reference' => true,
        'no_useless_else' => true,
        'standardize_not_equals' => true,
        'yoda_style' => [
            'always_move_variable' => false,
            'equal' => false,
            'identical' => false,
        ],
        'multiline_whitespace_before_semicolons' => [
            'strategy' => 'no_multi_line',
        ],
        'constant_case' => ['case' => 'lower'],
        'list_syntax' => ['syntax' => 'short'],
        'multiline_comment_opening_closing' => true,
        'single_line_empty_body' => false,
    ])
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setUsingCache(true)
    ->setCacheFile(__DIR__ . '/runtime/.php-cs-fixer.cache');
