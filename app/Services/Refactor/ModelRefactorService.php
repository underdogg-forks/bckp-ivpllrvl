<?php

namespace App\Services\Refactor;

use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use App\Services\Refactor\Traits\LogsRefactorChanges;

class ModelRefactorService
{
    use LogsRefactorChanges;

    public function refactor(string $modulesPath, bool $dry, string $log): int
    {
        $count = 0;
        $finder = (new Finder())->directories()->in($modulesPath)->depth(2)->name('Models');

        foreach ($finder as $modelDir) {
            foreach (File::files($modelDir->getRealPath()) as $file) {
                $name = pathinfo($file, PATHINFO_FILENAME);

                // Skip if not a PHP file
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                // Process ALL .php files in Models directories
                $serviceName = $this->serviceName($name);
                $serviceDir = dirname($modelDir->getRealPath()) . '/Services';
                $newPath = $serviceDir . '/' . $serviceName . '.php';

                if (! File::exists($serviceDir) && ! $dry) {
                    File::makeDirectory($serviceDir, 0777, true);
                }

                if (! $dry) {
                    // Move the file to Services directory
                    File::move($file->getRealPath(), $newPath);
                    $this->updateNamespace($newPath);

                    // Create corresponding Eloquent model
                    $eloquentName = Str::studly($name);
                    app('Illuminate\Contracts\Console\Kernel')->call('make:model', [
                        'name' => $eloquentName,
                        '-mfs' => true
                    ]);
                }

                $this->writeLog($log, 'Model moved: ' . $name . ' → ' . $serviceName);
                $count++;
            }
        }
        return $count;
    }

    private function serviceName(string $original): string
    {
        // Convert any model name to Service naming
        if (Str::startsWith(Str::lower($original), 'mdl_')) {
            return Str::studly(Str::after($original, 'mdl_')) . 'Service';
        }

        return $original . 'Service';
    }

    private function updateNamespace(string $filePath): void
    {
        $c = File::get($filePath);
        $module = basename(dirname(dirname($filePath)));
        $ns = "Modules\\{$module}\\Services";

        // Update namespace from Models to Services
        $c = preg_replace('/^namespace\s+Modules\\\\[^\\\\]+\\\\Models;/m', "namespace {$ns};", $c);

        // Also handle cases without existing namespace
        if (!str_contains($c, 'namespace')) {
            $c = preg_replace('/^<\?php/', "<?php\n\nnamespace {$ns};", $c);
        }

        File::put($filePath, $c);
    }
}
