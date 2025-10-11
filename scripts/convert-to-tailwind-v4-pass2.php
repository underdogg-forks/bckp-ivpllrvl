#!/usr/bin/env php
<?php

/**
 * Tailwind CSS v4 Conversion Script - Pass 2
 * 
 * Handles edge cases and dynamic class assignments:
 * - Remaining btn-primary/btn-default in ternary operators
 * - Standalone 'btn' classes
 * - Label status classes
 */

// Second pass conversions
$replacements = [
    // Remaining static button combos
    '/\bclass="([^"]*?)btn px-3 py-1\.5 text-sm btn-primary([^"]*)"/i' => 'class="$1inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors$2"',
    
    '/\bclass="([^"]*?)btn px-3 py-1\.5 text-sm btn-default([^"]*)"/i' => 'class="$1inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors$2"',
    
    '/\bclass="([^"]*?)btn px-2 py-1 text-xs btn-link([^"]*)"/i' => 'class="$1inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors$2"',
    
    // Dynamic button classes with conditions - convert to full Tailwind
    '/class="btn {{([^}]+)\? \'btn-primary\' : \'btn-default\'([^}]*)}}"/' => 'class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$1? \'bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500\' : \'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500\'$2}}"',
    
    // Label status classes with dynamic values
    '/class="label {{([^}]+)}}"/i' => 'class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{$1}}"',
    
    // Clean up standalone btn classes that got left behind
    '/\bclass="([^"]*)\bbtn\b(?!\s*px-)([^"]*)"/i' => 'class="$1$2"',
    
    // Fix submenu-row that got broken
    '/class="submenu- flex flex-wrap -mx-4"/i' => 'class="submenu-row"',
];

function convertFilePass2($filePath, $replacements, $dryRun = false) {
    if (!file_exists($filePath)) {
        echo "File not found: $filePath\n";
        return false;
    }
    
    $content = file_get_contents($filePath);
    $originalContent = $content;
    $changes = 0;
    
    foreach ($replacements as $pattern => $replacement) {
        $newContent = preg_replace($pattern, $replacement, $content, -1, $count);
        if ($newContent !== null && $newContent !== $content) {
            $content = $newContent;
            $changes += $count;
        }
    }
    
    // Clean up multiple spaces in class attributes
    $content = preg_replace_callback('/class="([^"]+)"/', function($matches) {
        $classes = preg_split('/\s+/', trim($matches[1]));
        $classes = array_filter($classes); // Remove empty
        $classes = array_unique($classes); // Remove duplicates
        return 'class="' . implode(' ', $classes) . '"';
    }, $content);
    
    if ($content !== $originalContent && !$dryRun) {
        file_put_contents($filePath, $content);
        return $changes;
    }
    
    return $changes;
}

// Main execution
$dryRun = in_array('--dry-run', $argv);
$verbose = in_array('--verbose', $argv) || in_array('-v', $argv);

echo "Tailwind CSS v4 Conversion Script - Pass 2\n";
echo "==========================================\n\n";

if ($dryRun) {
    echo "DRY RUN MODE - No files will be modified\n\n";
}

// Find all blade files
$bladeFiles = [];
$directories = ['resources/views', 'Modules'];

foreach ($directories as $dir) {
    $fullPath = __DIR__ . '/../' . $dir;
    if (is_dir($fullPath)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($fullPath, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($iterator as $file) {
            if ($file->getExtension() === 'php' && str_ends_with($file->getFilename(), '.blade.php')) {
                $bladeFiles[] = $file->getPathname();
            }
        }
    }
}

echo "Found " . count($bladeFiles) . " blade files\n\n";

$totalChanges = 0;
$filesModified = 0;

foreach ($bladeFiles as $file) {
    $changes = convertFilePass2($file, $replacements, $dryRun);
    
    if ($changes > 0) {
        $filesModified++;
        $totalChanges += $changes;
        
        if ($verbose) {
            echo "✓ " . basename($file) . " ($changes changes)\n";
        }
    }
}

echo "\n==========================================\n";
echo "Conversion Complete!\n";
echo "Files modified: $filesModified\n";
echo "Total changes: $totalChanges\n";

if ($dryRun) {
    echo "\nRun without --dry-run to apply changes\n";
}
