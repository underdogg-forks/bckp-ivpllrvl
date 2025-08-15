<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Refactor\BladeRefactorService;
use App\Services\Refactor\ModelRefactorService;
use App\Services\Refactor\ControllerRefactorService;
use App\Services\Refactor\HelperRefactorService;

class FixmeRefactorCommand extends Command
{
    protected $signature = 'project:fixme
        {--dry-run : Show changes without applying them}
        {--blade : Repair Blade views}
        {--models : Move CI models to Services and create Eloquent models}
        {--controllers : Adjust controllers to Services and Eloquent}
        {--helpers : Route helper calls through Helpers class}
        {--all : Run all steps}';

    protected $description = 'CodeIgniter → Laravel refactor suite';

    public function handle(
        BladeRefactorService $blade,
        ModelRefactorService $models,
        ControllerRefactorService $controllers,
        HelperRefactorService $helpers
    ): int {
        $dry = (bool) $this->option('dry-run');
        $log = storage_path('logs/project_refactor.log');

        $runAll = (bool) $this->option('all');
        $runBlade = $runAll || (bool) $this->option('blade');
        $runModels = $runAll || (bool) $this->option('models');
        $runControllers = $runAll || (bool) $this->option('controllers');
        $runHelpers = $runAll || (bool) $this->option('helpers');

        $totals = [
            'blade' => 0,
            'models' => 0,
            'controllers' => 0,
            'helpers' => 0,
        ];

        if ($runBlade) {
            $totals['blade'] += $blade->refactor(base_path('resources/views'), $dry, $log);
            $totals['blade'] += $blade->refactor(base_path('Modules'), $dry, $log);
            $this->line('Blade repaired: ' . $totals['blade']);
        }

        if ($runModels) {
            $totals['models'] += $models->refactor(base_path('Modules'), $dry, $log);
            $this->line('Models processed: ' . $totals['models']);
        }

        if ($runControllers) {
            $totals['controllers'] += $controllers->refactor(base_path('Modules'), $dry, $log);
            $this->line('Controllers updated: ' . $totals['controllers']);
        }

        if ($runHelpers) {
            $totals['helpers'] += $helpers->refactor(base_path(), $dry, $log);
            $this->line('Helper calls routed: ' . $totals['helpers']);
        }

        $this->info('Done: ' . json_encode($totals));
        return self::SUCCESS;
    }
}
