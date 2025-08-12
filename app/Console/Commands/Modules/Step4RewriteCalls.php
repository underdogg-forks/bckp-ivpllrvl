<?php

namespace App\Console\Commands\Modules;

use App\Support\Modules\PhpAst\CallSiteRewriter;
use Illuminate\Support\Facades\File;

class Step4RewriteCalls extends BaseModuleCommand
{
    protected $signature = 'modules:step4:rewrite-calls';

    protected $description = 'Rewrite all call-sites across Modules/ according to method maps';

    public function handle(CallSiteRewriter $rewriter): int
    {
        $flat = [];
        foreach ($this->modulesFromTarget() as $module) {
            $mapFile = storage_path("module_refactor/{$module}/methods.json");
            if ( ! File::exists($mapFile)) {
                continue;
            }
            $perFile = json_decode(File::get($mapFile), true) ?? [];
            foreach ($perFile as $fileMap) {
                foreach ($fileMap as $old => $new) {
                    $flat[$old] = $new;
                }
            }
        }

        if ( ! $flat) {
            $this->warn('No method maps found.');

            return self::SUCCESS;
        }

        $rewriter->rewriteTree(base_path($this->modulesRoot), $flat);
        $this->info('Call-sites rewritten.');

        return self::SUCCESS;
    }
}
