#!/usr/bin/env php
<?php

/**
 * Tailwind CSS v4 Conversion Script.
 *
 * Converts Bootstrap classes to Tailwind CSS v4 with:
 * - Dark mode support (dark: variants)
 * - Responsive design (mobile: sm:, tablet: md:, desktop: lg:, xl:, 2xl:)
 * - No hardcoded colors
 * - Semantic color scales
 */

// Bootstrap to Tailwind CSS v4 mapping with dark mode
$classMap = [
    // Buttons
    'btn btn-default' => 'inline-flex items-center gap-2 px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors',

    'btn btn-primary' => 'inline-flex items-center gap-2 px-4 py-2 bg-blue-600 dark:bg-blue-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-blue-700 dark:hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors',

    'btn btn-success' => 'inline-flex items-center gap-2 px-4 py-2 bg-green-600 dark:bg-green-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-green-700 dark:hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors',

    'btn btn-danger' => 'inline-flex items-center gap-2 px-4 py-2 bg-red-600 dark:bg-red-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-red-700 dark:hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors',

    'btn btn-warning' => 'inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 dark:bg-yellow-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-yellow-700 dark:hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors',

    'btn btn-info' => 'inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 dark:bg-cyan-500 border border-transparent rounded-md text-sm font-medium text-white hover:bg-cyan-700 dark:hover:bg-cyan-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-cyan-500 transition-colors',

    'btn btn-link' => 'inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:underline focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors',

    // Button sizes
    'btn-sm' => 'px-3 py-1.5 text-sm',
    'btn-xs' => 'px-2 py-1 text-xs',
    'btn-lg' => 'px-6 py-3 text-base',

    // Button groups
    'btn-group'    => 'inline-flex rounded-md shadow-sm',
    'btn-group-sm' => 'inline-flex rounded-md shadow-sm [&>*]:px-3 [&>*]:py-1.5 [&>*]:text-sm',

    // Panels/Cards
    'panel panel-default' => 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm',
    'panel panel-info'    => 'bg-white dark:bg-gray-800 border border-cyan-200 dark:border-cyan-700 rounded-lg shadow-sm',
    'panel panel-danger'  => 'bg-white dark:bg-gray-800 border border-red-200 dark:border-red-700 rounded-lg shadow-sm',

    'panel-heading' => 'px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900',
    'panel-title'   => 'text-lg font-medium text-gray-900 dark:text-gray-100',
    'panel-body'    => 'p-6',
    'panel-footer'  => 'px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900',

    // Tables
    'table table-hover table-striped' => 'min-w-full divide-y divide-gray-200 dark:divide-gray-700',
    'table table-hover'               => 'min-w-full divide-y divide-gray-200 dark:divide-gray-700',
    'table table-striped'             => 'min-w-full divide-y divide-gray-200 dark:divide-gray-700',
    'table table-bordered'            => 'min-w-full divide-y divide-gray-200 dark:divide-gray-700 border border-gray-200 dark:border-gray-700',
    'table table-condensed'           => 'min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm',
    'table-responsive'                => 'overflow-x-auto',

    // Alerts
    'alert alert-success' => 'p-4 mb-4 text-green-700 dark:text-green-200 bg-green-100 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-lg',
    'alert alert-danger'  => 'p-4 mb-4 text-red-700 dark:text-red-200 bg-red-100 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-lg',
    'alert alert-warning' => 'p-4 mb-4 text-yellow-700 dark:text-yellow-200 bg-yellow-100 dark:bg-yellow-900/50 border border-yellow-200 dark:border-yellow-800 rounded-lg',
    'alert alert-info'    => 'p-4 mb-4 text-cyan-700 dark:text-cyan-200 bg-cyan-100 dark:bg-cyan-900/50 border border-cyan-200 dark:border-cyan-800 rounded-lg',

    // Forms
    'form-group'      => 'mb-4',
    'form-control'    => 'w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-blue-500 dark:focus:border-blue-400 sm:text-sm transition-colors',
    'form-horizontal' => 'space-y-4',
    'form-inline'     => 'flex flex-wrap gap-4 items-center',

    // Labels (status badges)
    'label label-success' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-200',
    'label label-danger'  => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-800 dark:text-red-200',
    'label label-warning' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200',
    'label label-info'    => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 dark:bg-cyan-900/50 text-cyan-800 dark:text-cyan-200',
    'label label-default' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200',

    // Dropdowns
    'dropdown-menu'   => 'absolute z-10 mt-2 min-w-[160px] bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg overflow-hidden',
    'dropdown-toggle' => 'inline-flex items-center gap-2',
    'dropdown-button' => 'w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors',

    // Grid
    'row'       => 'flex flex-wrap -mx-4',
    'col-xs-12' => 'w-full px-4',
    'col-xs-6'  => 'w-1/2 px-4',
    'col-xs-4'  => 'w-1/3 px-4',
    'col-xs-3'  => 'w-1/4 px-4',
    'col-md-12' => 'w-full md:w-full px-4',
    'col-md-6'  => 'w-full md:w-1/2 px-4',
    'col-md-4'  => 'w-full md:w-1/3 px-4',
    'col-md-3'  => 'w-full md:w-1/4 px-4',
    'col-lg-12' => 'w-full lg:w-full px-4',
    'col-lg-6'  => 'w-full lg:w-1/2 px-4',
    'col-lg-4'  => 'w-full lg:w-1/3 px-4',
    'col-lg-3'  => 'w-full lg:w-1/4 px-4',

    // Utilities
    'pull-right'  => 'float-right',
    'pull-left'   => 'float-left',
    'text-right'  => 'text-right',
    'text-left'   => 'text-left',
    'text-center' => 'text-center',
    'clearfix'    => 'clear-both',
    'hidden-xs'   => 'hidden sm:block',
    'hidden-sm'   => 'sm:hidden md:block',
    'hidden-md'   => 'md:hidden lg:block',
    'hidden-lg'   => 'lg:hidden',
    'visible-xs'  => 'block sm:hidden',
    'visible-sm'  => 'hidden sm:block md:hidden',
    'visible-md'  => 'hidden md:block lg:hidden',
    'visible-lg'  => 'hidden lg:block',
];

// Additional pattern-based replacements
$patterns = [
    // Table elements
    '/(<thead[^>]*>)/i'              => '$1',
    '/(<tbody[^>]*>)/i'              => '$1',
    '/(<tr[^>]*class=")([^"]*)(")/i' => function ($matches) {
        $classes = $matches[2];
        if (empty(mb_trim($classes))) {
            return $matches[1] . 'hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors' . $matches[3];
        }

        return $matches[0];
    },

    // Table headers
    '/(<th[^>]*class=")([^"]*)(")/i' => function ($matches) {
        $classes    = $matches[2];
        $newClasses = 'px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-800';
        if ( ! empty(mb_trim($classes)) && ! preg_match('/px-|py-|text-/', $classes)) {
            return $matches[1] . $newClasses . ' ' . $classes . $matches[3];
        }

        return $matches[1] . $newClasses . $matches[3];
    },

    // Table cells
    '/(<td[^>]*class=")([^"]*)(")/i' => function ($matches) {
        $classes    = $matches[2];
        $newClasses = 'px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100';
        if ( ! empty(mb_trim($classes)) && ! preg_match('/px-|py-|text-/', $classes)) {
            return $matches[1] . $newClasses . ' ' . $classes . $matches[3];
        }

        return $matches[1] . $newClasses . $matches[3];
    },
];

function convertFile($filePath, $classMap, $patterns, $dryRun = false)
{
    if ( ! file_exists($filePath)) {
        echo "File not found: {$filePath}\n";

        return false;
    }

    $content         = file_get_contents($filePath);
    $originalContent = $content;
    $changes         = 0;

    // Sort class map by length (longest first) to avoid partial replacements
    uksort($classMap, function ($a, $b) {
        return mb_strlen($b) - mb_strlen($a);
    });

    // First pass: Handle dynamic classes with ternary operators
    // Pattern: class="btn {{ $var ? 'btn-primary' : 'btn-default' }}"
    $dynamicPatterns = [
        '/class="([^"]*?)btn\s+{{\s*([^}]+?)\s*\?\s*\'btn-primary\'\s*:\s*\'btn-default\'\s*}}([^"]*)"/i' => function ($matches) use (&$changes) {
            $before    = mb_trim($matches[1]);
            $condition = $matches[2];
            $after     = mb_trim($matches[3]);
            $changes++;

            $defaultClasses = 'inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors';
            $baseClasses    = mb_trim($before . ' ' . $defaultClasses . ' ' . $after);

            return 'class="' . $baseClasses . ' {{ ' . $condition . ' ? \'bg-blue-600 dark:bg-blue-500 text-white border-transparent hover:bg-blue-700 dark:hover:bg-blue-600 focus:ring-blue-500\' : \'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500\' }}"';
        },
        '/class="([^"]*?)btn\s+{{\s*([^}]+?)\s*\?\s*\'btn-success\'\s*:\s*\'btn-default\'\s*}}([^"]*)"/i' => function ($matches) use (&$changes) {
            $before    = mb_trim($matches[1]);
            $condition = $matches[2];
            $after     = mb_trim($matches[3]);
            $changes++;

            $defaultClasses = 'inline-flex items-center gap-2 px-4 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors';
            $baseClasses    = mb_trim($before . ' ' . $defaultClasses . ' ' . $after);

            return 'class="' . $baseClasses . ' {{ ' . $condition . ' ? \'bg-green-600 dark:bg-green-500 text-white border-transparent hover:bg-green-700 dark:hover:bg-green-600 focus:ring-green-500\' : \'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:ring-blue-500\' }}"';
        },
    ];

    foreach ($dynamicPatterns as $pattern => $replacement) {
        $content = preg_replace_callback($pattern, $replacement, $content);
    }

    // Replace class mappings
    foreach ($classMap as $bootstrap => $tailwind) {
        $pattern     = '/class="([^"]*)\b' . preg_quote($bootstrap, '/') . '\b([^"]*)"/i';
        $replacement = function ($matches) use ($tailwind, &$changes) {
            $before = $matches[1];
            $after  = $matches[2];
            $changes++;

            // Clean up extra spaces
            $newClass = mb_trim($before . ' ' . $tailwind . ' ' . $after);
            $newClass = preg_replace('/\s+/', ' ', $newClass);

            return 'class="' . $newClass . '"';
        };

        $newContent = preg_replace_callback($pattern, $replacement, $content);
        if ($newContent !== null) {
            $content = $newContent;
        }
    }

    // Clean up duplicate classes
    $content = preg_replace_callback('/class="([^"]+)"/', function ($matches) {
        $classes = preg_split('/\s+/', $matches[1]);
        $classes = array_unique($classes);

        return 'class="' . implode(' ', $classes) . '"';
    }, $content);

    // Check for hardcoded colors (validation)
    if (preg_match('/\b(bg|text|border)-\[#[0-9A-Fa-f]{3,6}\]/i', $content)) {
        echo "WARNING: Hardcoded color found in {$filePath}\n";
    }

    if ($content !== $originalContent && ! $dryRun) {
        file_put_contents($filePath, $content);

        return $changes;
    }

    return $changes;
}

// Main execution
$dryRun  = in_array('--dry-run', $argv);
$verbose = in_array('--verbose', $argv) || in_array('-v', $argv);

echo "Tailwind CSS v4 Conversion Script\n";
echo "=================================\n\n";

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
    $changes = convertFile($file, $classMap, $patterns, $dryRun);

    if ($changes > 0) {
        $filesModified++;
        $totalChanges += $changes;

        if ($verbose) {
            echo '✓ ' . basename($file) . " ({$changes} changes)\n";
        }
    }
}

echo "\n=================================\n";
echo "Conversion Complete!\n";
echo "Files modified: {$filesModified}\n";
echo "Total changes: {$totalChanges}\n";

if ($dryRun) {
    echo "\nRun without --dry-run to apply changes\n";
}

echo "\nNext steps:\n";
echo "1. Review the changes with: git diff\n";
echo "2. Build the project: npm run build\n";
echo "3. Test in both light and dark modes\n";
echo "4. Test on mobile and desktop screen sizes\n";
