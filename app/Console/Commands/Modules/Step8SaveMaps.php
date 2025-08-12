<?php

namespace App\Console\Commands\Modules;

use Illuminate\Support\Facades\File;
use stdClass;

class Step8SaveMaps extends BaseModuleCommand
{
    protected $signature = 'modules:step8:save-maps';

    protected $description = 'Aggregate and save per-module mapping JSON (methods + routes)';

    public function handle(): int
    {
        foreach ($this->modulesFromTarget() as $module) {
            $methods = storage_path("module_refactor/{$module}/methods.json");
            $routes  = storage_path("module_refactor/{$module}/routes.json");

            $out = [
                'module'  => $module,
                'methods' => File::exists($methods) ? json_decode(File::get($methods), true) : new stdClass(),
                'routes'  => File::exists($routes) ? json_decode(File::get($routes), true) : [],
            ];

            $this->writeJson("module_mappings/{$module}.json", $out);
            $this->info("Saved storage/module_mappings/{$module}.json");
        }

        return self::SUCCESS;
    }
}
