<?php

namespace App\Console\Commands\Modules;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

abstract class BaseModuleCommand extends Command
{
    protected string $legacyRoot = 'application/modules';

    protected string $modulesRoot = 'Modules';

    protected function modulesFromLegacy(): array
    {
        $path = base_path($this->legacyRoot);
        if ( ! File::exists($path)) {
            return [];
        }

        return collect(File::directories($path))
            ->map(fn (string $p) => basename($p))
            ->values()
            ->all();
    }

    protected function modulesFromTarget(): array
    {
        $path = base_path($this->modulesRoot);
        if ( ! File::exists($path)) {
            return [];
        }

        return collect(File::directories($path))
            ->map(fn (string $p) => basename($p))
            ->values()
            ->all();
    }

    protected function studly(string $name): string
    {
        return Str::studly($name);
    }

    protected function kebab(string $name): string
    {
        return Str::of($name)->snake()->replace('_', '-')->toString();
    }

    protected function writeJson(string $relativePath, array $data): void
    {
        $abs = storage_path($relativePath);
        File::ensureDirectoryExists(dirname($abs));
        File::put($abs, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
