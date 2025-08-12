<?php

namespace App\Console\Commands\Modules;

use App\Support\Modules\Views\BladeConverter;
use Illuminate\Support\Facades\File;

class Step5BladeConvert extends BaseModuleCommand
{
    protected $signature = 'modules:step5:bladeify';

    protected $description = 'Convert legacy PHP views to Blade; remove __() in favor of @lang()';

    public function handle(BladeConverter $conv): int
    {
        foreach ($this->modulesFromTarget() as $module) {
            // Support both Resources/views and lowercase resources/views
            $paths = [
                base_path("{$this->modulesRoot}/{$module}/Resources/views"),
                base_path("{$this->modulesRoot}/{$module}/resources/views"),
                base_path("{$this->modulesRoot}/{$module}/views"),
            ];
            foreach ($paths as $p) {
                if ( ! File::isDirectory($p)) {
                    continue;
                }
                $conv->convertDir($p, inPlace: true);
            }
        }
        $this->info('Views converted to Blade.');

        return self::SUCCESS;
    }
}
