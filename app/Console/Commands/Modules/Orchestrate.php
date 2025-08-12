<?php

namespace App\Console\Commands\Modules;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Orchestrate extends Command
{
    protected $signature = 'modules:orchestrate {--move}';

    protected $description = 'Run full migration pipeline for all modules';

    public function handle(): int
    {
        $steps = [
            ['php', 'artisan', 'modules:step1:rsync', $this->option('move') ? '--move' : null],
            ['composer', 'dump-autoload'],
            ['php', 'artisan', 'modules:step2:rename-files'],
            ['php', 'artisan', 'modules:step3:namespace-methods'],
            ['php', 'artisan', 'modules:step4:rewrite-calls'],
            ['php', 'artisan', 'modules:step5:bladeify'],
            ['php', 'artisan', 'modules:step6:routes'],
            ['php', 'artisan', 'modules:step8:save-maps'],
            ['composer', 'rector'],
            ['composer', 'lint'],
        ];

        foreach ($steps as $cmd) {
            $cmd = array_values(array_filter($cmd, fn ($x) => null !== $x));
            $this->info('> ' . implode(' ', $cmd));
            $p = new Process($cmd, base_path());
            $p->setTimeout(null);
            $p->mustRun(function ($type, $buffer) { $this->output->write($buffer); });
        }

        $this->info('Modules orchestration complete.');

        return self::SUCCESS;
    }
}
