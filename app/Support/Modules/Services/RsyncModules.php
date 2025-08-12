<?php

namespace App\Support\Modules\Services;

use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class RsyncModules
{
    public function run(string $legacyRoot, string $modulesRoot, bool $move = false): void
    {
        $src = base_path($legacyRoot);
        $dst = base_path($modulesRoot);

        if ( ! File::exists($src)) {
            return;
        }
        File::ensureDirectoryExists($dst);

        $args = $move ? ['rsync', '-a', '--remove-source-files', $src . '/', $dst . '/']
            : ['rsync', '-a', $src . '/', $dst . '/'];

        (new Process($args, base_path()))->mustRun();
    }
}
