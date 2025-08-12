<?php

namespace App\Console\Commands\Helpers;

use App\Support\Helpers\FunctionCallRewriter;
use App\Support\Helpers\HelperClassGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class WrapHelpersCommand extends Command
{
    protected $signature = 'Helpers:wrap
        {--source=application/Helpers}
        {--namespace-dir=Helpers}
        {--bc-dir=Helpers/bc}
        {--rewrite-calls : Rewrite function calls to static class calls}
        {--roots=app,Modules : Comma-separated roots to rewrite when --rewrite-calls is set}';

    protected $description = 'Wrap global helper functions into namespaced classes with static methods, generate BC wrappers, optionally rewrite call sites';

    public function handle(HelperClassGenerator $gen, FunctionCallRewriter $rewriter): int
    {
        $source = (string) $this->option('source');
        $nsDir  = (string) $this->option('namespace-dir');
        $bcDir  = (string) $this->option('bc-dir');

        if ( ! File::isDirectory(base_path($source))) {
            $this->warn("Missing directory: {$source}");

            return self::SUCCESS;
        }

        $map = $gen->generateFromDir($source, $nsDir);
        if ( ! $map) {
            $this->info('No helper functions found.');

            return self::SUCCESS;
        }

        $gen->writeBackwardCompat($bcDir, $map);

        if ((bool) $this->option('rewrite-calls')) {
            $roots = array_map('trim', explode(',', (string) $this->option('roots')));
            $rewriter->rewriteTree($roots, $map);
        }

        $this->info('Helpers wrapped, BC file written, call sites ' . ($this->option('rewrite-calls') ? 'rewritten' : 'unchanged') . '.');

        return self::SUCCESS;
    }
}
