<?php

namespace App\Services\Refactor;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use App\Services\Refactor\Traits\LogsRefactorChanges;

class ControllerRefactorService
{
    use LogsRefactorChanges;

    public function refactor(string $modulesPath, bool $dry, string $log, Command $command): int
    {
        $count = 0;
        $moduleDirectories = File::directories($modulesPath);

        foreach ($moduleDirectories as $moduleDir) {
            $controllerDir = $moduleDir . DIRECTORY_SEPARATOR . 'Controllers';

            if (File::isDirectory($controllerDir)) {
                foreach (File::files($controllerDir) as $file) {
                    $contents = File::get($file->getRealPath());
                    $originalContents = $contents;

                    $command->info("  - Refactoring controller: {$file->getRelativePathname()}");

                    // Refactor 1: Convert layout pattern to return view()
                    $contents = $this->convertLayouts($contents);

                    // Refactor 2a: Convert realpath to storage_path
                    $contents = preg_replace("/realpath\('UPLOADS_ARCHIVE_FOLDER'\)/", "storage_path('app/uploads/archives')", $contents);

                    // Refactor 2b: Convert CI logging to Laravel's Log facade
                    $contents = preg_replace("/log_message\('error', (.+?)\);/", "Log::error($1);", $contents);

                    // Refactor 3: Convert $this->mdl_ calls to new Service() calls
                    $contents = $this->convertServices($contents);

                    if ($contents !== $originalContents) {
                        if (!$dry) {
                            File::put($file->getRealPath(), $contents);
                            $this->addLogFacade($file->getRealPath());
                        }
                        $this->writeLog($log, "Refactored controller: {$file->getRelativePathname()}");
                        $count++;
                    }
                }
            }
        }
        return $count;
    }

    private function convertLayouts(string $contents): string
    {
        // Find the full layout block and replace with return view()
        $pattern = "/\\\$this->layout->set\(\[(.+?)\]\);\s*\\\$this->layout->buffer\('content',\s*'(.+?)'\);\s*\\\$this->layout->render\(\);/s";

        $contents = preg_replace_callback($pattern, function($matches) {
            $variables = trim($matches[1]);
            $viewPath = Str::replace('/', '.', $matches[2]);
            return "return view('{$viewPath}', [{$variables}]);";
        }, $contents);

        return $contents;
    }

    private function convertServices(string $contents): string
    {
        // Convert all $this->mdl_ calls
        $contents = preg_replace_callback(
            "/\\\$this->mdl_([a-z0-9_]+)->/i",
            function($matches) {
                $serviceName = Str::studly($matches[1]);
                return "(new {$serviceName}Service())->";
            },
            $contents
        );
        return $contents;
    }

    private function addLogFacade(string $filePath): void
    {
        $contents = File::get($filePath);
        if (!Str::contains($contents, 'use Illuminate\Support\Facades\Log;')) {
            $contents = preg_replace('/(namespace\s+.*?;)/s', "$1\n\nuse Illuminate\Support\Facades\Log;", $contents, 1);
            File::put($filePath, $contents);
        }
    }
}
