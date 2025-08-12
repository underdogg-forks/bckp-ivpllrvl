<?php

namespace App\Console\Commands\Modules;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

/**
 * Step 2 — Rename module directories and PHP files to PSR-12 friendly StudlyCase.
 *
 * Rules
 * - Rename ALL directories under each module to StudlyCase (deepest-first).
 * - Rename ALL PHP files to StudlyCase filenames (ClassName.php).
 * - EXCLUDE view templates (Resources/views, resources/views, views).
 * - EXCLUDE route files under Modules/*    /Routes (those follow {modulename}.php convention).
 * - Use File facade for every FS operation (no file_*).
 * - Be robust on case-insensitive filesystems (temporary hop rename when only case differs).
 */
class Step2RenameFiles extends BaseModuleCommand
{
    protected $signature = 'modules:step2:rename-files';

    protected $description = 'Rename module directories and PHP files to StudlyCase (PSR-12), excluding views and route files.';

    public function handle(): int
    {
        $modules = $this->modulesFromTarget();
        if (empty($modules)) {
            $this->warn('No modules found under ' . $this->modulesRoot);

            return self::SUCCESS;
        }

        foreach ($modules as $module) {
            $root = base_path("{$this->modulesRoot}/{$module}");
            if ( ! File::isDirectory($root)) {
                $this->warn("Skip (not a directory): {$root}");
                continue;
            }

            $this->renameDirectoriesDeepestFirst($root);

            $this->renamePhpFiles($root);
        }

        $this->info('Directories and PHP files Studly-cased (views and route files untouched).');

        return self::SUCCESS;
    }

    // -------------------------
    // Directories
    // -------------------------
    private function renameDirectoriesDeepestFirst(string $moduleRoot): void
    {
        $dirs = collect(
            Finder::create()
                ->in($moduleRoot)
                ->directories()
        )
            ->map(fn ($dir) => $dir->getRealPath())
            ->values();

        foreach ($dirs as $dir) {
            $base   = basename($dir);
            $studly = Str::studly($base);

            if ($this->isRoutesDir($dir)) {
                $studly = 'Routes';
            }

            if ($this->isResourcesDir($dir)) {
                $studly = 'Resources';
            }

            if ($base === $studly) {
                continue; // already correct
            }

            $target = dirname($dir) . DIRECTORY_SEPARATOR . $studly;

            if ($this->pathsEqualCaseInsensitive($dir, $target)) {
                continue;
            }

            if (File::exists($target)) {
                // Conflict: log and skip
                $this->warn("Dir rename conflict, exists: {$target} (from {$dir})");
                continue;
            }

            $this->safeMove($dir, $target);
            $this->line("Dir  : {$dir}  ->  {$target}");
        }
    }

    // -------------------------
    // Files
    // -------------------------

    private function renamePhpFiles(string $moduleRoot): void
    {
        $files = collect(File::allFiles($moduleRoot))
            // only .php
            ->filter(fn ($f) => Str::endsWith($f->getFilename(), '.php'))
            // exclude views and route files
            ->reject(fn ($f) => $this->isViewPath($f->getPathname()) || $this->isRouteFile($f->getPathname()))
            // process shallowest-first so class files move before siblings if needed
            ->sortBy(fn ($f) => mb_substr_count($f->getPathname(), DIRECTORY_SEPARATOR))
            ->values();

        foreach ($files as $f) {
            $path     = $f->getPathname();
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $studly   = Str::studly($filename);

            // Only rename the filename (not extension or path)
            $target = $f->getPath() . DIRECTORY_SEPARATOR . $studly . '.php';

            if ($this->pathsEqualCaseInsensitive($path, $target)) {
                continue; // already correct
            }

            if (File::exists($target)) {
                // Conflict: log and skip
                $this->warn("File rename conflict, exists: {$target} (from {$path})");
                continue;
            }

            $this->safeMove($path, $target);
            $this->line("File : {$path}  ->  {$target}");
        }
    }

    // -------------------------
    // Helpers
    // -------------------------
    private function isViewPath(string $path): bool
    {
        $norm = $this->normalize($path);

        // match */Resources/views/* OR */resources/views/* OR */views/*
        return Str::contains($norm, '/resources/views/')
            || Str::contains($norm, '/resources/view/')
            || Str::contains($norm, '/resources/views')
            || Str::contains($norm, '/resources/view')
            || (Str::contains($norm, '/resources/')
                && (Str::endsWith($norm, '/views') || Str::contains($norm, '/views/')))
            || Str::contains($norm, '/views/');
    }

    private function isRoutesDir(string $dir): bool
    {
        $norm = $this->normalize($dir);

        return Str::endsWith($norm, '/routes') || Str::endsWith($norm, '/routes/');
    }

    private function isResourcesDir(string $dir): bool
    {
        $norm = $this->normalize($dir);

        return Str::endsWith($norm, '/resources') || Str::endsWith($norm, '/resources/');
    }

    private function isRouteFile(string $path): bool
    {
        $norm = $this->normalize($path);

        return (bool) preg_match('#/modules/[^/]+/routes/[^/]+\.php$#i', $norm);
    }

    private function normalize(string $path): string
    {
        $p = str_replace('\\', '/', $path);

        return mb_strtolower($p);
    }

    /**
     * Robust move that works on case-insensitive filesystems when only name casing changes.
     */
    private function safeMove(string $from, string $to): void
    {
        // If only case differs, hop via a temp name
        if (strcasecmp($from, $to) === 0 && $from !== $to) {
            $temp = $from . '.renaming.' . uniqid('', true);
            File::move($from, $temp);
            File::move($temp, $to);

            return;
        }

        File::move($from, $to);
    }

    private function pathsEqualCaseInsensitive(string $a, string $b): bool
    {
        if ( ! File::exists($a)) {
            return false;
        }
        if ($a === $b) {
            return true;
        }

        return strcasecmp($a, $b) === 0;
    }
}
