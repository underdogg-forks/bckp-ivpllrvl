#!/usr/bin/env php
<?php

/**
 * Tailwind CSS v4 Conversion Script - Pass 3 (Final Cleanup).
 *
 * Handles remaining edge cases:
 * - Dynamic classes with '-default', '-primary', '-success', '-danger', '-link'
 * - Custom button classes with specific IDs
 */
$replacements = [
    // Fix broken dynamic classes: 'btn-primary' : '-default' → proper Tailwind
    "/class=\"btn {{([^}]+)'btn-primary' : '-default'([^}]*)}}\"/i" => 'class="inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors {{$1\'bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500\' : \'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500\'$2}}"',

    // Fix broken static classes with hyphen prefix
    '/\bclass="([^"]*?)btn px-3 py-1\.5 text-sm -success([^"]*)"/i' => 'class="$1inline-flex items-center gap-2 px-3 py-1.5 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors$2"',

    '/\bclass="([^"]*?)btn px-2 py-1 text-xs -danger([^"]*)"/i' => 'class="$1inline-flex items-center gap-1 px-2 py-1 bg-red-600 dark:bg-red-500 border border-transparent rounded-sm text-xs font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors$2"',

    '/\bclass="([^"]*?)btn px-2 py-1 text-xs -default([^"]*)"/i' => 'class="$1inline-flex items-center gap-1 px-2 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-sm text-xs font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors$2"',

    '/\bclass="([^"]*?)btn px-2 py-1 text-xs -link([^"]*)"/i' => 'class="$1inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors$2"',

    // Fix remaining standalone btn classes
    '/\bclass="([^"]*?)\bbtn\b(?!\s*{{)([^"]*)"/i' => function ($matches) {
        $before = mb_trim($matches[1]);
        $after  = mb_trim($matches[2]);

        // If there's already Tailwind classes, just remove btn
        if (preg_match('/\b(inline-flex|px-|py-|bg-|text-|border-)/i', $before . $after)) {
            return 'class="' . mb_trim($before . ' ' . $after) . '"';
        }

        // Otherwise add basic button styling
        return 'class="' . mb_trim($before . ' inline-flex items-center gap-2 px-4 py-2 ' . $after) . '"';
    },
];

function convertFilePass3($filePath, $replacements, $dryRun = false)
{
    if ( ! file_exists($filePath)) {
        echo "File not found: {$filePath}\n";

        return false;
    }

    $content         = file_get_contents($filePath);
    $originalContent = $content;
    $changes         = 0;

    foreach ($replacements as $pattern => $replacement) {
        if (is_callable($replacement)) {
            $newContent = preg_replace_callback($pattern, $replacement, $content, -1, $count);
        } else {
            $newContent = preg_replace($pattern, $replacement, $content, -1, $count);
        }

        if ($newContent !== null && $newContent !== $content) {
            $content = $newContent;
            $changes += $count;
        }
    }

    // Clean up multiple spaces in class attributes
    $content = preg_replace_callback('/class="([^"]+)"/', function ($matches) {
        $classes = preg_split('/\s+/', mb_trim($matches[1]));
        $classes = array_filter($classes); // Remove empty
        $classes = array_unique($classes); // Remove duplicates

        return 'class="' . implode(' ', $classes) . '"';
    }, $content);

    if ($content !== $originalContent && ! $dryRun) {
        file_put_contents($filePath, $content);

        return $changes;
    }

    return $changes;
}

// Main execution
$dryRun  = in_array('--dry-run', $argv);
$verbose = in_array('--verbose', $argv) || in_array('-v', $argv);

echo "Tailwind CSS v4 Conversion Script - Pass 3 (Final)\n";
echo "=================================================\n\n";

if ($dryRun) {
    echo "DRY RUN MODE - No files will be modified\n\n";
}

// Find all blade files
$bladeFiles  = [];
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

echo 'Found ' . count($bladeFiles) . " blade files\n\n";

$totalChanges  = 0;
$filesModified = 0;

foreach ($bladeFiles as $file) {
    $changes = convertFilePass3($file, $replacements, $dryRun);

    if ($changes > 0) {
        $filesModified++;
        $totalChanges += $changes;

        if ($verbose) {
            echo '✓ ' . basename($file) . " ({$changes} changes)\n";
        }
    }
}

echo "\n=================================================\n";
echo "Final Cleanup Complete!\n";
echo "Files modified: {$filesModified}\n";
echo "Total changes: {$totalChanges}\n";

if ($dryRun) {
    echo "\nRun without --dry-run to apply changes\n";
}
