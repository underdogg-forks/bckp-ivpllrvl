<?php

namespace App\Services\Refactor;

use Symfony\Component\Finder\Finder;
use Illuminate\Support\Facades\File;
use App\Services\Refactor\Traits\LogsRefactorChanges;

class BladeRefactorService
{
    use LogsRefactorChanges;

    public function refactor(string $path, bool $dry, string $log): int
    {
        $count = 0;
        $finder = (new Finder())->files()->in($path)->name('*.blade.php');
        foreach ($finder as $file) {
            $rel = $file->getRealPath();
            if (! $this->isBladeTarget($rel)) {
                continue;
            }
            $contents = File::get($rel);
            $updated = $this->repairBlade($contents);
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
        if (preg_match('#Modules[/\\\\][^/\\\\]+[/\\\\]Resources[/\\\\]views[/\\\\]#', $fullPath)) {
            return true;
        }
        return false;
    }

    private function repairBlade(string $c): string
    {
        // Fix double @ symbols
        $c = preg_replace('/@@lang/', '@lang', $c);

        // Convert include paths from slash to dot notation
        $c = preg_replace("/@include\s*\(\s*['\"]([^'\"]+)\/([^'\"]+)['\"]\s*\)/", "@include('$1.$2')", $c);

        // Remove PHP namespace declarations in Blade files
        $c = preg_replace('/^@php\s+namespace\s+.*?;?\s+@endphp/s', '', $c);

        return $c;
    }
}
