<?php

namespace App\Console\Commands\Modules;

use App\Support\Modules\PhpAst\MethodCamelizer;
use Illuminate\Support\Facades\File;

class Step3NamespaceAndMethods extends BaseModuleCommand
{
    protected $signature = 'modules:step3:namespace-methods';

    protected $description = 'Inject namespaces, Studly classes, camelCase methods + PHPDoc (original name/file)';

    public function handle(MethodCamelizer $camelizer): int
    {
        $report = [];

        foreach ($this->modulesFromTarget() as $module) {
            $root  = base_path("{$this->modulesRoot}/{$module}");
            $files = File::allFiles($root);

            $moduleMap = [];
            foreach ($files as $f) {
                if ($f->getExtension() !== 'php') {
                    continue;
                }

                $map = $camelizer->processFile($f->getRealPath(), $this->modulesRoot, "Modules\\{$module}");
                if ($map) {
                    $moduleMap[$f->getRelativePathname()] = $map;
                }
            }

            if ($moduleMap) {
                $this->writeJson("module_refactor/{$module}/methods.json", $moduleMap);
                $report[$module] = $moduleMap;
            }
        }

        $this->info('Namespaces + methods converted. Method maps saved under storage/module_refactor/*/methods.json');

        return self::SUCCESS;
    }
}
