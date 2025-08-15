<?php

namespace App\Services\Refactor\Traits;

use Illuminate\Support\Facades\File;

trait LogsRefactorChanges
{
    protected function writeLog(string $log, string $line): void
    {
        File::append($log, '[' . now() . '] ' . $line . PHP_EOL);
    }

    protected function putIfChanged(string $path, string $new, bool $dry, string $log, string $what): bool
    {
        $old = File::get($path);
        if ($new === $old) {
            return false;
        }
        $this->writeLog($log, $what . ': ' . $path);
        if ($dry) {
            return true;
        }
        File::put($path, $new);

        return true;
    }
}
