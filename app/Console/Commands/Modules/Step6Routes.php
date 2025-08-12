<?php

namespace App\Console\Commands\Modules;

use App\Support\Modules\Routes\RouteWriter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use Symfony\Component\Finder\Finder;
use Throwable;

class Step6Routes extends BaseModuleCommand
{
    protected $signature = 'modules:step6:routes {--debug}';

    protected $description = 'Generate Modules/{Module}/Routes/{modulename}.php from public controller methods';

    public function handle(RouteWriter $writer): int
    {
        $debug  = (bool) $this->option('debug');
        $parser = (new ParserFactory())->createForHostVersion();

        foreach ($this->modulesFromTarget() as $module) {
            $ctrlDir = base_path("{$this->modulesRoot}/{$module}/Controllers");
            if ( ! File::isDirectory($ctrlDir)) {
                if ($debug) {
                    $this->warn("[{$module}] no Controllers dir");
                }
                continue;
            }

            $files  = Finder::create()->files()->in($ctrlDir)->name('*.php');
            $routes = [];

            foreach ($files as $f) {
                $code = File::get($f->getRealPath());
                try {
                    $ast = $parser->parse($code);
                    if ( ! $ast) {
                        continue;
                    }
                } catch (Throwable $e) {
                    if ($debug) {
                        $this->warn("[{$module}] parse error: {$f->getRelativePathname()} :: {$e->getMessage()}");
                    }
                    continue;
                }

                $fqcn = $this->fqcn($ast, "Modules\\{$module}\\Controllers");
                if ( ! $fqcn) {
                    if ($debug) {
                        $this->warn("[{$module}] no class in {$f->getRelativePathname()}");
                    }
                    continue;
                }

                $methods = $this->publicMethods($ast);
                if ( ! $methods) {
                    if ($debug) {
                        $this->line("[{$module}] no public methods: {$f->getRelativePathname()}");
                    }
                    continue;
                }

                foreach ($methods as $m) {
                    $camel    = $this->camel($m);
                    $http     = $this->guessHttp($m);
                    $uri      = $this->guessUri($module, $m);
                    $name     = $this->guessRouteName($module, $camel);
                    $routes[] = [
                        'controller' => $fqcn,
                        'method'     => $camel,
                        'http'       => $http,
                        'uri'        => $uri,
                        'route'      => $name,
                    ];
                }
            }

            if ($routes) {
                $writer->write($module, $routes);
                $this->writeJson("module_refactor/{$module}/routes.json", $routes);
                $this->info("Routes written for {$module}.");
            } elseif ($debug) {
                $this->warn("[{$module}] no routes discovered");
            }
        }

        return self::SUCCESS;
    }

    private function fqcn(array $ast, string $fallbackNs): ?string
    {
        $ns    = $fallbackNs;
        $class = null;

        foreach ($ast as $n) {
            if ($n instanceof Node\Stmt\Namespace_) {
                $ns = $n->name?->toString() ?: $fallbackNs;
                foreach ($n->stmts as $s) {
                    if ($s instanceof Node\Stmt\Class_ && ! $s->isAnonymous() && ! $s->isAbstract()) {
                        $class = (string) $s->name;
                    }
                }
            } elseif ($n instanceof Node\Stmt\Class_ && ! $n->isAnonymous() && ! $n->isAbstract()) {
                $class = (string) $n->name;
            }
        }

        return $class ? "{$ns}\\{$class}" : null;
    }

    private function publicMethods(array $ast): array
    {
        $out = [];
        $tr  = new NodeTraverser();
        $tr->addVisitor(new class ($out) extends NodeVisitorAbstract {
            public function __construct(private array &$out) {}

            public function enterNode(Node $node)
            {
                if ($node instanceof Node\Stmt\ClassMethod && $node->isPublic()) {
                    $n = (string) $node->name;
                    if ( ! in_array($n, ['__construct', '__invoke'], true)) {
                        $this->out[] = $n;
                    }
                }
            }
        });
        $tr->traverse($ast);

        return array_values(array_unique($out));
    }

    private function camel(string $s): string
    {
        $p = preg_split('/[_\-]+/', $s);

        return $p[0] . collect(array_slice($p, 1))->map(fn ($t) => ucfirst($t))->implode('');
    }

    private function guessHttp(string $m): string
    {
        $m = mb_strtolower($m);
        if (in_array($m, [
            'store', 'update', 'destroy', 'save', 'post', 'upload', 'create',
            'create_recurring', 'create_credit', 'change_user', 'change_client',
            'copy_invoice', 'delete_item', 'save_invoice_tax_rate',
        ], true)) {
            return 'POST';
        }
        if (str_starts_with($m, 'post') || str_starts_with($m, 'save') || str_starts_with($m, 'upload')) {
            return 'POST';
        }

        return 'GET';
    }

    private function guessUri(string $module, string $orig): string
    {
        $slug = Str::of($module)->snake('-')->replace('_', '-')->lower()->toString();

        return $orig === 'index' ? $slug : $slug . '/' . Str::of($orig)->snake('-')->toString();
    }

    private function guessRouteName(string $module, string $camel): string
    {
        $prefix = Str::of($module)->snake('-')->replace('_', '-')->lower()->toString();

        return $camel === 'index' ? "{$prefix}.index" : "{$prefix}." . Str::of($camel)->snake('-')->toString();
    }
}
