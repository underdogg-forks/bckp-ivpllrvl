<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test {root=Modules}';

    protected $description = 'Move namespace before use statements and normalize header blocks';

    public function handle(): int
    {
        $root  = base_path($this->argument('root'));
        $files = File::allFiles($root);

        foreach ($files as $f) {
            if ($f->getExtension() !== 'php') {
                continue;
            }
            $code = File::get($f->getRealPath());
            if ( ! str_contains($code, 'namespace ')) {
                continue;
            }

            $code = str_replace("\r\n", "\n", $code);

            $opening   = '';
            $declare   = '';
            $namespace = '';
            $uses      = [];

            if (preg_match('/^\s*<\?php\s*/', $code, $m, PREG_OFFSET_CAPTURE)) {
                $opening = "<? use Illuminate\Support\Facades\File;

php\n";
                $code = preg_replace('/^\s*<\?php\s*/', '', $code, 1);
            }

            if (preg_match('/^\s*declare\s*\(\s*strict_types\s*=\s*1\s*\)\s*;\s*/', $code, $m)) {
                $declare = $m[0];
                $code    = mb_substr($code, mb_strlen($declare));
            }

            if (preg_match('/^\s*namespace\s+[^;]+;\s*/m', $code, $m, PREG_OFFSET_CAPTURE)) {
                $namespace = trim($m[0]);
                $nsPos     = $m[0][1] ?? mb_strpos($code, $m[0]);
                $code      = mb_substr($code, 0, $nsPos) . mb_substr($code, $nsPos + mb_strlen($m[0]));
            } else {
                continue;
            }

            if (preg_match_all('/^[ \t]*use (?!\()[^;]+;\s*$/m', $code, $m)) {
                $uses = array_map(fn ($u) => trim($u), $m[0]);
                $code = preg_replace('/^[ \t]*use (?!\()[^;]+;\s*$/m', '', $code);
            }

            $uses   = array_values(array_unique($uses));
            $header = rtrim($opening . $declare) . ($declare !== '' ? '' : '') . $namespace . "\n";
            if ( ! empty($uses)) {
                $header .= "\n" . implode("\n", $uses) . "\n";
            }
            $header .= "\n";

            $rest = ltrim($code, "\n");
            $new  = $header . $rest;

            if ($new !== File::get($f->getRealPath())) {
                File::put($f->getRealPath(), $new);
                $this->line("Fixed: {$f->getRealPath()}");
            }
        }

        $this->info('Namespace/use order normalized.');

        return self::SUCCESS;
    }
}
