<?php

namespace App\Console\Commands\Modules;

use App\Support\Modules\Services\RsyncModules;

class Step1Rsync extends BaseModuleCommand
{
    protected $signature = 'modules:step1:rsync {--move}';

    protected $description = 'rsync application/modules → Modules';

    public function handle(RsyncModules $rsync): int
    {
        $rsync->run($this->legacyRoot, $this->modulesRoot, (bool) $this->option('move'));
        $this->info('Synced legacy modules into Modules/.');

        return self::SUCCESS;
    }
}
