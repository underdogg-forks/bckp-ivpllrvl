<?php

namespace App\Support\Modules\Routes;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RouteWriter
{
    public function write(string $moduleName, array $routes): void
    {
        $dir = base_path("Modules/{$moduleName}/Routes");
        File::ensureDirectoryExists($dir);

        $slug = Str::of($moduleName)->snake('-')->replace('_', '-')->lower()->toString();
        $file = "{$dir}/{$slug}.php";

        $uses = array_values(array_unique(array_map(fn ($r) => $r['controller'], $routes)));
        sort($uses);

        $out   = [];
        $out[] = '<?php';
        $out[] = 'use Illuminate\\Support\\Facades\\Route;';
        foreach ($uses as $fqcn) {
            $out[] = "use {$fqcn};";
        }
        $out[] = '';
        $out[] = "Route::middleware('web')->group(function () {";
        foreach ($routes as $r) {
            $http  = mb_strtolower($r['http']);
            $short = ltrim(mb_strrchr($r['controller'], '\\'), '\\');
            $out[] = "    Route::{$http}('{$r['uri']}', [{$short}::class, '{$r['method']}'])->name('{$r['route']}');";
        }
        $out[] = '};' === '' ? '' : '});';
        $out[] = '';

        File::put($file, implode("\n", $out));
    }
}
