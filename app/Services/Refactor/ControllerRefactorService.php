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

        $command->line('Found ' . count($moduleDirectories) . ' module directories.');

        foreach ($moduleDirectories as $moduleDir) {
            $controllerDir = $moduleDir . DIRECTORY_SEPARATOR . 'Controllers';

            if (File::isDirectory($controllerDir)) {
                $files = File::files($controllerDir);
                $command->comment('Processing ' . basename($moduleDir) . ' module: ' . count($files) . ' controllers found.');

                foreach ($files as $file) {
                    $contents = File::get($file->getRealPath());
                    $originalContents = $contents;

                    $command->info("  - Refactoring controller: {$file->getRelativePathname()}");

                    // Refactor 1: Convert CI redirect() to Laravel's route() helper
                    $contents = preg_replace("/redirect\('(.+?)'\);/", "redirect()->route('$1');", $contents);

                    // Refactor 2: Convert CI Query Builder to Eloquent using callbacks
                    $contents = $this->convertQueries($contents);

                    if ($contents !== $originalContents) {
                        if (!$dry) {
                            File::put($file->getRealPath(), $contents);
                        }
                        $this->writeLog($log, "Refactored controller: {$file->getRelativePathname()}");
                        $count++;
                    }
                }
            }
        }
        return $count;
    }

    private function convertQueries(string $contents): string
    {
        // Example: $this->db->get('invoices') -> Invoice::all()
        $contents = preg_replace_callback(
            "/\$this->db->get\('(.+?)'\)/",
            function ($matches) {
                $model = Str::studly(Str::singular($matches[1]));
                return "{$model}::all()";
            },
            $contents
        );

        // Example: $this->db->get_where('invoices', array('id' => $id)) -> Invoice::where('id', $id)->first()
        $contents = preg_replace_callback(
            "/\$this->db->get_where\('(.+?)', array\('(.+?)'\s*=>\s*([^)]+)\)\)/",
            function ($matches) {
                $model = Str::studly(Str::singular($matches[1]));
                $column = trim($matches[2], "'");
                $value = trim($matches[3]);
                return "{$model}::where('{$column}', {$value})->first()";
            },
            $contents
        );

        // Example: $this->db->insert('invoices', $data) -> Invoice::create($data)
        $contents = preg_replace_callback(
            "/\$this->db->insert\('(.+?)',\s*([^)]+)\)/",
            function ($matches) {
                $model = Str::studly(Str::singular($matches[1]));
                $data = $matches[2];
                return "{$model}::create({$data})";
            },
            $contents
        );

        return $contents;
    }
}
