<?php

namespace App\Console\Commands;

use App\Services\Refactor\BladeRefactorService;
use App\Services\Refactor\ControllerRefactorService;
use App\Services\Refactor\HelperRefactorService;
use App\Services\Refactor\ModelRefactorService;
use App\Services\Refactor\ServiceRepairService;
use Illuminate\Console\Command;

class FixmeRefactorCommand extends Command
{
    protected $signature = 'project:fixme
        {--dry-run : Show changes without applying them}
        {--blade : Repair Blade views}
        {--models : Move CI models to Services and create Eloquent models}
        {--controllers : Adjust controllers to Services and Eloquent}
        {--helpers : Route helper calls through Helpers class}
        {--repair : Repair services that were already moved}
        {--all : Run all steps}';

    protected $description = 'CodeIgniter → Laravel refactor suite';

    public function handle(
        BladeRefactorService $blade,
        ModelRefactorService $models,
        ControllerRefactorService $controllers,
        HelperRefactorService $helpers,
        ServiceRepairService $repair // Add this line
    ): int {
        $dry = (bool) $this->option('dry-run');
        $log = storage_path('logs/project_refactor.log');

        $runAll         = (bool) $this->option('all');
        $runBlade       = $runAll || (bool) $this->option('blade');
        $runModels      = $runAll || (bool) $this->option('models');
        $runControllers = $runAll || (bool) $this->option('controllers');
        $runHelpers     = $runAll || (bool) $this->option('helpers');
        $runRepair      = $runAll || (bool) $this->option('repair'); // Add this line

        $totals = [
            'blade'       => 0,
            'models'      => 0,
            'controllers' => 0,
            'helpers'     => 0,
            'services'    => 0, // Add this line
        ];

        if ($runBlade) {
            $this->info('Starting Blade refactor...');
            $totals['blade'] += $blade->refactor(base_path('resources/views'), $dry, $log, $this);
            $totals['blade'] += $blade->refactor(base_path('Modules'), $dry, $log, $this);
            $this->info("Blade repaired: {$totals['blade']}");
        }

        if ($runModels) {
            $this->info('Starting Models refactor...');
            $totals['models'] += $models->refactor(base_path('Modules'), $dry, $log, $this);
            $this->info("Models processed: {$totals['models']}");
        }

        if ($runControllers) {
            $this->info('Starting Controllers refactor...');
            $totals['controllers'] += $controllers->refactor(base_path('Modules'), $dry, $log, $this);
            $this->info("Controllers updated: {$totals['controllers']}");
        }

        if ($runHelpers) {
            $this->info('Starting Helper refactor...');
            $totals['helpers'] += $helpers->refactor(base_path(), $dry, $log, $this);
            $this->info("Helper calls routed: {$totals['helpers']}");
        }

        if ($runRepair) {
            $this->info('Starting Services repair...');
            $totals['services'] += $repair->repair(base_path('Modules'), $dry, $log, $this);
            $this->info("Services repaired: {$totals['services']}");
        }

        $this->info('Done: ' . json_encode($totals));

        return self::SUCCESS;
    }
}
