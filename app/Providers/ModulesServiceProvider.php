<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class ModulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $base = base_path('Modules');
        if ( ! is_dir($base)) {
            return;
        }

        foreach (scandir($base) as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }
            $path = $base . DIRECTORY_SEPARATOR . $module;
            if ( ! is_dir($path)) {
                continue;
            }

            // Config merge
            $configDir = $path . '/Config';
            if (is_dir($configDir)) {
                foreach (File::files($configDir) as $file) {
                    $key = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                    $this->mergeConfigFrom($file->getPathname(), "modules.{$module}.{$key}");
                }
            }
        }
    }

    public function boot(): void
    {
        $base = base_path('Modules');
        if ( ! is_dir($base)) {
            return;
        }

        foreach (scandir($base) as $module) {
            if ($module === '.' || $module === '..') {
                continue;
            }
            $path = $base . DIRECTORY_SEPARATOR . $module;
            if ( ! is_dir($path)) {
                continue;
            }

            $slug      = Str::of($module)->snake('-')->replace('_', '-')->lower()->toString();
            $routeFile = $path . '/Routes/' . $slug . '.php';
            if (is_file($routeFile)) {
                Route::middleware('web')->group($routeFile);
            }

            $views = $path . '/Resources/views';
            if (is_dir($views)) {
                View::addNamespace(mb_strtolower($module), $views);
            }

            $lang = $path . '/Resources/lang';
            if (is_dir($lang)) {
                $this->loadTranslationsFrom($lang, mb_strtolower($module));
            }

            $migrations = $path . '/Database/Migrations';
            if (is_dir($migrations)) {
                $this->loadMigrationsFrom($migrations);
            }
        }
    }
}
