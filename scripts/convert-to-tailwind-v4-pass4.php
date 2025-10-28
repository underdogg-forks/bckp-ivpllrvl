#!/usr/bin/env php
<?php

/**
 * Tailwind CSS v4 Conversion Script - Pass 4 (Dynamic Classes Fix).
 *
 * Properly handles dynamic button classes with ternary operators
 */
function convertFilePass4($filePath, $dryRun = false)
{
    if ( ! file_exists($filePath)) {
        return false;
    }

    $content         = file_get_contents($filePath);
    $originalContent = $content;
    $changes         = 0;

    // Pattern: class="btn {{ condition ? ' prefix -primary' : '-default'" }}>
    $pattern = '/class="btn {{ ([^}]+?)\? ([\'"])([^\'"]*)-primary\2 : ([\'"])([^\'"]*)-default\4" }}/';

    $content = preg_replace_callback($pattern, function ($matches) use (&$changes) {
        $condition     = mb_trim($matches[1]);
        $primaryPrefix = mb_trim($matches[3]);
        $defaultPrefix = mb_trim($matches[5]);

        $changes++;

        // Build the full Tailwind classes
        $primaryClasses = 'bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500';
        $defaultClasses = 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500';

        // Base classes that are always present
        $baseClasses = 'inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors';

        // Combine
        $primaryFull = mb_trim($primaryPrefix . ' ' . $primaryClasses);
        $defaultFull = mb_trim($defaultPrefix . ' ' . $defaultClasses);

        return 'class="' . $baseClasses . ' {{' . $condition . '? \'' . $primaryFull . '\' : \'' . $defaultFull . '\'}}"';
    }, $content);

    // Clean up multiple spaces in class attributes
    $content = preg_replace_callback('/class="([^"]+)"/', function ($matches) {
        $classes = preg_replace('/\s+/', ' ', mb_trim($matches[1]));

        return 'class="' . $classes . '"';
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

echo "Tailwind CSS v4 Conversion Script - Pass 4 (Dynamic Classes)\n";
echo "===========================================================\n\n";

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
    $changes = convertFilePass4($file, $dryRun);

    if ($changes > 0) {
        $filesModified++;
        $totalChanges += $changes;

        if ($verbose) {
            echo '✓ ' . basename($file) . " ({$changes} changes)\n";
        }
    }
}

echo "\n===========================================================\n";
echo "Dynamic Classes Fix Complete!\n";
echo "Files modified: {$filesModified}\n";
echo "Total changes: {$totalChanges}\n";

if ($dryRun) {
    echo "\nRun without --dry-run to apply changes\n";
}
