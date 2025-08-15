<?php

namespace App\Services\Refactor;

use App\Services\Refactor\Traits\LogsRefactorChanges;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class ServiceRepairService
{
    use LogsRefactorChanges;

    public function repair(string $modulesPath, bool $dry, string $log, Command $command): int
    {
        $count  = 0;
        $finder = (new Finder())->files()->in($modulesPath)->path('/Services/')->name('*.php');
        $command->line('Found ' . count($finder) . ' service files to inspect.');

        foreach ($finder as $file) {
            $contents         = File::get($file->getRealPath());
            $originalContents = $contents;
            $fileName         = pathinfo($file, PATHINFO_FILENAME);

            // Fix 1: Ensure class name ends in 'Service'
            $oldName  = preg_replace('/Service$/', '', $fileName);
            $contents = preg_replace("/class\s+{$oldName}/", "class {$fileName}", $contents);

            // Fix 2: Ensure it extends BaseService
            if (Str::contains($contents, 'extends')) {
                $contents = preg_replace("/extends\s+[A-Za-z_][A-Za-z0-9_]+/", 'extends BaseService', $contents);
            } else {
                $contents = preg_replace("/class\s+{$fileName}/", "class {$fileName} extends BaseService", $contents);
            }

            // Fix 3: Add the use statement for BaseService
            $namespace = 'App\\Services\\BaseService;';
            if ( ! Str::contains($contents, $namespace)) {
                $contents = preg_replace('/(namespace\s+.*?;)/s', "$1\n\nuse {$namespace}", $contents, 1);
            }

            if ($contents !== $originalContents) {
                if ( ! $dry) {
                    File::put($file->getRealPath(), $contents);
                }
                $this->writeLog($log, "Repaired service file: {$file->getRelativePathname()}");
                $command->info("  - Repaired: {$file->getRelativePathname()}");
                $count++;
            }
        }

        return $count;
    }
}
