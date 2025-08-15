<?php

namespace App\Console\Commands\Modules;

use App\Services\Refactor\BladeConverter;
use Illuminate\Support\Facades\File;

class Step5BladeConvert extends BaseModuleCommand
{
    protected $signature = 'modules:step5:bladeify {--project-views : Also convert project resources/views}';

    protected $description = 'Convert legacy PHP views to Blade; apply CI→Blade rules and TODO fixes';

    public function handle(BladeConverter $conv): int
    {
        foreach ($this->modulesFromTarget() as $module) {
            $paths = [
                base_path("{$this->modulesRoot}/{$module}/Resources/views"),
                base_path("{$this->modulesRoot}/{$module}/resources/views"),
                base_path("{$this->modulesRoot}/{$module}/views"),
            ];

            foreach ($paths as $p) {
                if (File::isDirectory($p)) {
                    $conv->convertDir($p, inPlace: true);
                }
            }
        }

        if ($this->option('project-views')) {
            $rootViews = resource_path('views');
            if (File::isDirectory($rootViews)) {
                $conv->convertDir($rootViews, inPlace: false);
            }
        }

        $this->info('Views converted to Blade.');

        return self::SUCCESS;
    }
}
