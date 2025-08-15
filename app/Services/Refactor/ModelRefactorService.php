<?php

namespace App\Services\Refactor;

use App\Services\Refactor\Traits\LogsRefactorChanges;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ModelRefactorService
{
    use LogsRefactorChanges;

    public function refactor(string $modulesPath, bool $dry, string $log, Command $command): int
    {
        $count             = 0;
        $moduleDirectories = File::directories($modulesPath);
        $command->line('Found ' . count($moduleDirectories) . ' module directories.');

        foreach ($moduleDirectories as $moduleDir) {
            $moduleName = basename($moduleDir);
            $modelDir   = $moduleDir . DIRECTORY_SEPARATOR . 'Models';
            $command->comment("Processing module: {$moduleName}");

            if (File::isDirectory($modelDir)) {
                foreach (File::files($modelDir) as $file) {
                    if ($file->getExtension() !== 'php' || ! Str::endsWith($file->getFilename(), '.php')) {
                        continue;
                    }

                    $oldName      = pathinfo($file, PATHINFO_FILENAME);
                    $serviceName  = $this->serviceName($oldName);
                    $eloquentName = $this->eloquentName($oldName);

                    $command->info("  - Refactoring model: {$oldName} -> {$serviceName} / {$eloquentName}");

                    $contents = File::get($file->getRealPath());

                    // Fix 1 & 2: Update class name and inheritance
                    $contents = preg_replace("/class\s+{$oldName}\s+extends\s+ResponseModel/s", "class {$serviceName} extends BaseService", $contents);

                    $serviceDir = dirname($modelDir) . DIRECTORY_SEPARATOR . 'Services';
                    $newPath    = $serviceDir . DIRECTORY_SEPARATOR . $serviceName . '.php';

                    if ( ! $dry) {
                        if ( ! File::exists($serviceDir)) {
                            File::makeDirectory($serviceDir, 0777, true, true);
                        }
                        // Write the updated content to the new file path
                        File::put($newPath, $contents);
                        // Delete the old file
                        File::delete($file->getRealPath());

                        $this->updateNamespace($newPath);

                        // Fix 3: Run make:model command and check for output
                        $exitCode = Artisan::call('make:model', [
                            'name'        => $eloquentName,
                            '--migration' => true,
                            '--factory'   => true,
                            '--seed'      => true,
                        ]);

                        if ($exitCode === 0) {
                            $this->writeLog($log, 'Eloquent model created: ' . $eloquentName);
                        } else {
                            $this->writeLog($log, 'Failed to create Eloquent model: ' . $eloquentName . ' with exit code ' . $exitCode);
                        }
                    }

                    $this->writeLog($log, 'Model refactored: ' . $oldName . ' -> ' . $serviceName);
                    $count++;
                }
            } else {
                $command->line('  - No Models directory found.');
            }
        }

        return $count;
    }

    private function serviceName(string $original): string
    {
        return Str::studly($original) . 'Service';
    }

    private function eloquentName(string $original): string
    {
        return Str::studly($original);
    }

    private function updateNamespace(string $filePath): void
    {
        $c      = File::get($filePath);
        $module = basename(dirname($filePath, 2));
        $ns     = "Modules\\{$module}\\Services";

        $c = preg_replace('/^namespace\s+Modules\\\\[^\\\\]+\\\\Models;/m', "namespace {$ns};", $c);

        if ( ! str_contains($c, 'namespace')) {
            $c = preg_replace('/^<\?php/', "<?php\n\nnamespace {$ns};", $c);
        }

        File::put($filePath, $c);
    }
}
