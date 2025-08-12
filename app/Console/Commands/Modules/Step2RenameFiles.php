<?php

namespace App\Console\Commands\Modules;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class Step2RenameFiles extends BaseModuleCommand
{
    protected $signature = 'modules:step2:rename-files';

    protected $description = 'Rename PHP files to StudlyCase.php (PSR-12)';

    public function handle(): int
    {
        foreach ($this->modulesFromTarget() as $module) {
            $root  = base_path("{$this->modulesRoot}/{$module}");
            $files = collect(File::allFiles($root))->filter(fn ($f) => Str::endsWith($f->getFilename(), '.php'));

            foreach ($files as $f) {
                $name   = pathinfo($f->getFilename(), PATHINFO_FILENAME);
                $studly = Str::studly($name);
                $target = $f->getPath() . DIRECTORY_SEPARATOR . $studly . '.php';

                if ($target === $f->getRealPath()) {
                    continue;
                }
                if (File::exists($target)) {
                    continue;
                }

                @rename($f->getRealPath(), $target);
            }
        }
        $this->info('File names Studly-cased.');

        return self::SUCCESS;
    }
}
