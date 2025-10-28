#!/usr/bin/env php
<?php

/**
 * Convert hardcoded Tailwind color classes to theme-aware semantic classes.
 *
 * This script replaces hardcoded colors like bg-blue-600, bg-gray-700 with
 * semantic classes that respect theme colors (btn-primary, btn-default, etc.)
 */
$baseDir     = dirname(__DIR__);
$directories = [
    $baseDir . '/Modules',
    $baseDir . '/resources/views',
];

$stats = [
    'files_processed'    => 0,
    'files_modified'     => 0,
    'total_replacements' => 0,
];

// Pattern definitions for common button/link patterns
$patterns = [
    // Primary button pattern (blue background, white text)
    // Active/selected state buttons
    [
        'search'               => '/bg-blue-600\s+dark:bg-blue-500\s+.*?text-white.*?hover:bg-blue-700\s+dark:hover:bg-blue-600.*?focus:ring-blue-500/',
        'replace_with_context' => function ($match) {
            // Replace with btn-primary class
            return str_replace(
                ['bg-blue-600 dark:bg-blue-500', 'hover:bg-blue-700 dark:hover:bg-blue-600', 'focus:ring-blue-500'],
                ['btn-primary', '', ''],
                $match
            );
        },
        'description' => 'Primary button (blue) → btn-primary',
    ],

    // Default button pattern (white/gray background)
    // Inactive/unselected state buttons
    [
        'search'               => '/bg-white\s+dark:bg-gray-700\s+.*?text-gray-700\s+dark:text-gray-200.*?border-gray-300\s+dark:border-gray-600.*?hover:bg-gray-50\s+dark:hover:bg-gray-600/',
        'replace_with_context' => function ($match) {
            // Replace with btn-default class
            return str_replace(
                ['bg-white dark:bg-gray-700', 'text-gray-700 dark:text-gray-200', 'border-gray-300 dark:border-gray-600', 'hover:bg-gray-50 dark:hover:bg-gray-600', 'focus:ring-blue-500'],
                ['btn-default', '', '', '', ''],
                $match
            );
        },
        'description' => 'Default button (white/gray) → btn-default',
    ],
];

// Simple color replacements for ternary operations
$simpleReplacements = [
    // Primary state (active/selected)
    'bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500'                                          => 'btn-primary text-white border-transparent',
    'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500' => 'inline-flex items-center gap-2 px-4 py-2 btn-primary text-white border-transparent',

    // Default state (inactive/unselected)
    'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500' => 'btn-default',
];

function processFile($filePath)
{
    global $stats, $simpleReplacements;

    $content  = file_get_contents($filePath);
    $original = $content;

    // Apply simple replacements first (for ternary operations)
    foreach ($simpleReplacements as $search => $replace) {
        $count   = 0;
        $content = str_replace($search, $replace, $content, $count);
        if ($count > 0) {
            $stats['total_replacements'] += $count;
            echo "  ✓ Replaced '{$search}' → '{$replace}' ({$count} times)\n";
        }
    }

    // Additional individual class replacements
    $individualReplacements = [
        // Focus ring colors
        'focus:ring-blue-500' => '', // Remove since btn-primary/btn-default handle this

        // Standalone blue backgrounds (convert to primary)
        ['bg-blue-600', 'btn-primary'],
        ['dark:bg-blue-500', ''],
        ['hover:bg-blue-700', ''],
        ['dark:hover:bg-blue-600', ''],

        // Standalone gray backgrounds (convert to default)
        ['bg-gray-700', 'btn-default'],
        ['dark:bg-gray-700', ''],
        ['text-gray-700', ''],
        ['dark:text-gray-200', ''],
        ['border-gray-300', ''],
        ['dark:border-gray-600', ''],
        ['hover:bg-gray-50', ''],
        ['dark:hover:bg-gray-600', ''],
    ];

    // Check if file was modified
    if ($content !== $original) {
        file_put_contents($filePath, $content);
        $stats['files_modified']++;

        return true;
    }

    return false;
}

// Process all blade files
foreach ($directories as $directory) {
    if ( ! is_dir($directory)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory)
    );

    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php'
            && str_contains($file->getFilename(), '.blade.php')) {
            $filePath = $file->getPathname();
            $stats['files_processed']++;

            // Check if file contains hardcoded colors
            $content = file_get_contents($filePath);
            if (preg_match('/bg-blue-|text-blue-|bg-gray-|text-gray-|border-gray-/', $content)) {
                echo 'Processing: ' . str_replace($baseDir, '', $filePath) . "\n";

                if (processFile($filePath)) {
                    echo "  ✓ Modified\n";
                } else {
                    echo "  - No changes\n";
                }
            }
        }
    }
}

echo "\n";
echo "=====================================\n";
echo "Conversion Complete!\n";
echo "=====================================\n";
echo "Files processed: {$stats['files_processed']}\n";
echo "Files modified: {$stats['files_modified']}\n";
echo "Total replacements: {$stats['total_replacements']}\n";
echo "\n";
