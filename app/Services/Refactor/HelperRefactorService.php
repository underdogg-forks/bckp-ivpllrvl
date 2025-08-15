<?php

namespace App\Services\Refactor;

use App\Services\Refactor\Traits\LogsRefactorChanges;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class HelperRefactorService
{
    use LogsRefactorChanges;

    protected array $map = [
        // Add your helper function mappings here
        // 'old_function' => 'App\\Helpers\\SomeClass::newMethod'
    ];

    public function refactor(string $root, bool $dry, string $log): int
    {
        $count  = 0;
        $finder = (new Finder())->files()->in($root)->name('*.php');
        foreach ($finder as $file) {
            $path = $file->getRealPath();
            if (str_contains($path, 'vendor')) {
                continue;
            }
            $c       = File::get($path);
            $updated = $this->routeHelpers($c);
            if ($this->putIfChanged($path, $updated, $dry, $log, 'Helper calls routed')) {
                $count++;
            }
        }

        return $count;
    }

    private function routeHelpers(string $c): string
    {
        if (empty($this->map)) {
            return $c;
        }
        foreach ($this->map as $func => $fqcn) {
            $c = preg_replace('/(?<!::|\->|\$|\:)\\b' . preg_quote($func, '/') . '\\s*\\(/', $fqcn . '::' . $func . '(', $c);
        }

        return $c;
    }
}
