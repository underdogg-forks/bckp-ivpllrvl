<?php

namespace App\Services\Refactor;

use App\Services\Refactor\Traits\LogsRefactorChanges;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class BladeRefactorService
{
    use LogsRefactorChanges;

    protected BladeConverter $converter;

    public function __construct(BladeConverter $converter)
    {
        $this->converter = $converter;
    }

    public function refactor(string $path, bool $dry, string $log): int
    {
        $count  = 0;
        $finder = (new Finder())->files()->in($path)->name('*.php');
        foreach ($finder as $file) {
            $rel = $file->getRealPath();
            if ( ! $this->isBladeTarget($rel)) {
                continue;
            }

            // Check if the file is already a Blade file
            if (str_ends_with($rel, '.blade.php')) {
                continue;
            }

            // The BladeConverter handles the content and file renaming.
            $this->converter->convertDir(File::dirname($rel), true);

            // Since BladeConverter handles the file operations, we can just increment the count.
            $count++;
        }

        // We also need to process the files that are already .blade.php
        $bladeFinder = (new Finder())->files()->in($path)->name('*.blade.php');
        foreach ($bladeFinder as $file) {
            $rel = $file->getRealPath();
            if ( ! $this->isBladeTarget($rel)) {
                continue;
            }
            $contents = File::get($rel);
            $updated  = $this->converter->convertContent($contents);
            if ($this->putIfChanged($rel, $updated, $dry, $log, 'Blade fixed')) {
                $count++;
            }
        }

        return $count;
    }

    private function isBladeTarget(string $fullPath): bool
    {
        if (str_contains($fullPath, DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR)) {
            return true;
        }

        return (bool) (preg_match('#Modules[/\\\\][^/\\\\]+[/\\\\]Resources[/\\\\]views[/\\\\]#', $fullPath));
    }
}
