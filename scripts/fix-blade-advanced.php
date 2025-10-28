#!/usr/bin/env php
<?php

/**
 * Advanced Blade File Fixer Script
 * Converts complex PHP patterns to proper Blade syntax.
 */
$basePath = dirname(__DIR__);

// Get all blade files
$bladeFiles  = [];
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

echo 'Found ' . count($bladeFiles) . " blade files\n";

$fixedCount = 0;
$errorCount = 0;

foreach ($bladeFiles as $file) {
    try {
        $content         = file_get_contents($file);
        $originalContent = $content;

        // Pattern 1: Fix standalone 'if (' without @ to '@if('
        $content = preg_replace('/^(\s*)if\s*\(/m', '$1@if(', $content);

        // Pattern 2: Fix 'elseif (' to '@elseif('
        $content = preg_replace('/^(\s*)elseif\s*\(/m', '$1@elseif(', $content);

        // Pattern 3: Fix 'else {' to '@else'
        $content = preg_replace('/^(\s*)else\s*\{/m', '$1@else', $content);

        // Pattern 4: Fix 'foreach (' to '@foreach('
        $content = preg_replace('/^(\s*)foreach\s*\(/m', '$1@foreach(', $content);

        // Pattern 5: Convert {trans(text) : ...} to @lang('text'):
        $content = preg_replace('/{trans\(([a-z_]+)\)\s*:\s*/', '@lang(\'$1\'): ', $content);

        // Pattern 6: Convert {htmlsc(...) <br>} to {{ htmlsc(...) }}<br>
        $content = preg_replace('/{htmlsc\(([^)]+)\)\s*<br>}/', '{{ htmlsc($1) }}<br>', $content);

        // Pattern 7: Convert {$var <br>} to {{ $var }}<br>
        $content = preg_replace('/{(\$[a-zA-Z0-9_>-]+)\s*<br>}/', '{{ $1 }}<br>', $content);

        // Pattern 8: Fix broken @lang statements - @lang('text') }}; to @lang('text')
        $content = preg_replace('/@lang\(([^)]+)\)\s*}}/', '@lang($1)', $content);

        // Pattern 9: Convert echo statements with trans/htmlsc to blade
        $content = preg_replace('/echo\s+[\'"]:\s*[\'"]\s*\.\s*htmlsc\(([^)]+)\)\s*\.\s*[\'"]<br>[\'"]\s*;/', ': {{ htmlsc($1) }}<br>', $content);

        // Pattern 10: Remove trailing } from incomplete if statements
        $content = preg_replace('/@endif\s*}/', '@endif', $content);

        // Pattern 11: Convert PHP open tags with namespace to @php block
        if (preg_match('/<\?php\s+namespace\s+([^;]+);/', $content, $matches)) {
            $content = preg_replace('/<\?php\s+namespace\s+([^;]+);\s*/', '', $content);
        }

        // Pattern 12: Convert <?php includes to @php/@endphp with @include
        $content = preg_replace(
            '/<\?php\s+include\s+__DIR__\s*\.\s*[\'"]\/([^\'\"]+)\.php[\'"]\s*;\s*\?>/',
            '@php @endphp',
            $content
        );

        // Pattern 13: Fix broken class attributes with quotes
        $content = preg_replace('/class="\{\{\s*([^}]+)\s*\?\s*[\'"]([^\'"]+)[\'"]\s*:\s*[\'"]([^\'"]+)[\'"]/', 'class="{{ $1 ? \'$2\' : \'$3\'', $content);

        // Pattern 14: Clean up double }} or "> patterns
        $content = preg_replace('/}}\s*">/', '" }}>', $content);

        if ($content !== $originalContent) {
            file_put_contents($file, $content);
            $fixedCount++;
            echo '✓ Fixed: ' . str_replace($basePath, '', $file) . "\n";
        }
    } catch (Exception $e) {
        $errorCount++;
        echo '✗ Error in: ' . str_replace($basePath, '', $file) . ' - ' . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "Summary:\n";
echo "  Fixed: {$fixedCount} files\n";
echo "  Errors: {$errorCount} files\n";
echo '  Total: ' . count($bladeFiles) . " files\n";
