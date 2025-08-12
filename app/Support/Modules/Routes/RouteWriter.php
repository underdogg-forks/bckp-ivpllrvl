<?php

namespace App\Support\Modules\Routes;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RouteWriter
{
    /**
     * @param array<int,array{controller:string, method:string, http:string, uri:string, route:string}> $routes
     */
    public function write(string $moduleName, array $routes): void
    {
        $dir = base_path("Modules/{$moduleName}/Routes");
        File::ensureDirectoryExists($dir);

        $slug = Str::of($moduleName)->snake('-')->replace('_', '-')->lower()->toString();
        $file = "{$dir}/{$slug}.php";

        $lines = ['<?php', 'use Illuminate\\Support\\Facades\\Route;'];
        foreach ($routes as $r) {
            $lines[] = "use {$r['controller']};";
        }
        $lines[] = '';

        // Group per controller
        $byCtrl = collect($routes)->groupBy('controller');
        foreach ($byCtrl as $controller => $items) {
            $lines[] = 'Route::group([], function () {';
            foreach ($items as $r) {
                $http    = mb_strtolower($r['http']);
                $lines[] = "    Route::{$http}('{$r['uri']}', [{$r['controller']}::class, '{$r['method']}'])->name('{$r['route']}');";
            }
            $lines[] = "});\n";
        }

        File::put($file, implode("\n", $lines) . "\n");
    }
}
