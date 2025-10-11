#!/usr/bin/env php
<?php

/**
 * Blade File Fixer Script
 * Automatically fixes common blade syntax issues
 */

$basePath = dirname(__DIR__);

// Get all blade files
$bladeFiles = [];
$directories = [
    $basePath . '/resources/views',
];

// Add Modules directories
$modulesPath = $basePath . '/Modules';
if (is_dir($modulesPath)) {
    $modules = glob($modulesPath . '/*', GLOB_ONLYDIR);
    foreach ($modules as $module) {
        $viewsPath = $module . '/Resources/views';
        if (is_dir($viewsPath)) {
            $directories[] = $viewsPath;
        }
    }
}

// Collect all blade files
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir)
        );
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php' && str_ends_with($file->getFilename(), '.blade.php')) {
                $bladeFiles[] = $file->getPathname();
            }
        }
    }
}

echo "Found " . count($bladeFiles) . " blade files\n";

$fixedCount = 0;
$errorCount = 0;

foreach ($bladeFiles as $file) {
    try {
        $content = file_get_contents($file);
        $originalContent = $content;
        
        // Fix 1: Remove empty <?php tags at end of file
        $content = preg_replace('/<\?php\s*$/', '', $content);
        
        // Fix 2: Convert @php _function(); to {{ _function() }}
        $content = preg_replace('/@php\s+(_\w+\([^)]*\));\s*/', '{{ $1 }}', $content);
        
        // Fix 3: Fix broken title tags with semicolon and closing php tag
        $content = preg_replace('/({{[^}]+);\s*\?\>\s*}}/', '$1 }}', $content);
        
        // Fix 4: Convert href="@php _function(); " to href="{{ _function() }}"
        $content = preg_replace('/href="@php\s+([^;]+);\s*"/', 'href="{{ $1 }}"', $content);
        $content = preg_replace('/src="@php\s+([^;]+);\s*"/', 'src="{{ $1 }}"', $content);
        
        // Fix 5: Clean up .blade.blade.blade.php issues (already renamed)
        
        // Fix 6: Remove trailing empty <?php tags
        $content = preg_replace('/<\?php\s*\n*$/', '', $content);
        
        // Fix 7: Convert simple @php namespace lines (problematic)
        $content = preg_replace('/@php\s+namespace\s+[^;]+;\s*\n/', '', $content);
        
        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            $fixedCount++;
            echo "✓ Fixed: " . str_replace($basePath, '', $file) . "\n";
        }
        
    } catch (Exception $e) {
        $errorCount++;
        echo "✗ Error in: " . str_replace($basePath, '', $file) . " - " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "Summary:\n";
echo "  Fixed: $fixedCount files\n";
echo "  Errors: $errorCount files\n";
echo "  Total: " . count($bladeFiles) . " files\n";
