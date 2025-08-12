<?php

namespace App\Console\Commands\Modules;

use App\Support\Modules\Models\ModelScaffolder;

class Step7ModelScaffold extends BaseModuleCommand
{
    protected $signature = 'modules:step7:model {name=Invoice} {--resource}';

    protected $description = 'php artisan make:model {name} -mfs (uses stubs)';

    public function handle(ModelScaffolder $scaffolder): int
    {
        $name = (string) $this->argument('name');
        $scaffolder->make($name, (bool) $this->option('resource'));
        $this->info("Model {$name} scaffolded (-mfs).");

        return self::SUCCESS;
    }
}
