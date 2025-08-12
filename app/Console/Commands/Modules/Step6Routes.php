<?php

namespace App\Console\Commands\Modules;

use App\Support\Modules\Routes\RouteWriter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use Throwable;

class Step6Routes extends BaseModuleCommand
{
    protected $signature = 'modules:step6:routes';

    protected $description = 'Generate Modules/{Module}/Routes/{modulename}.php covering all public controller methods';

    public function handle(RouteWriter $writer): int
    {
        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);

        foreach ($this->modulesFromTarget() as $module) {
            $ctrlDir = base_path("{$this->modulesRoot}/{$module}/Controllers");
            if ( ! File::isDirectory($ctrlDir)) {
                continue;
            }

            $routes = [];
            foreach (File::allFiles($ctrlDir) as $f) {
                if ($f->getExtension() !== 'php') {
                    continue;
                }

                $code = File::get($f->getRealPath());
                try {
                    $ast = $parser->parse($code);
                } catch (Throwable) {
                    continue;
                }

                $fqcn    = $this->fqcn($ast, "Modules\\{$module}\\Controllers");
                $methods = $this->publicMethods($ast);

                $prefix = mb_strtolower($module); // e.g. invoices
                foreach ($methods as $m) {
                    $camel = $this->camel($m);
                    $http  = $this->guessHttp($m);
                    $uri   = $this->guessUri($module, $m);
                    $name  = $this->guessRouteName($module, $camel);

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
            }
        }

        return self::SUCCESS;
    }

    private function fqcn(array $ast, string $fallbackNs): string
    {
        $ns    = $fallbackNs;
        $class = null;
        foreach ($ast as $n) {
            if ($n instanceof Node\Stmt\Namespace_) {
                $ns = $n->name?->toString() ?? $fallbackNs;
                foreach ($n->stmts as $s) {
                    if ($s instanceof Node\Stmt\Class_) {
                        $class = (string) $s->name;
                    }
                }
            } elseif ($n instanceof Node\Stmt\Class_) {
                $class = (string) $n->name;
            }
        }

        return "{$ns}\\{$class}";
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
        if (in_array($m, ['store', 'update', 'destroy', 'save', 'post', 'upload', 'create', 'create_recurring', 'create_credit', 'change_user', 'change_client', 'copy_invoice', 'delete_item', 'save_invoice_tax_rate'])) {
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
        if ($orig === 'index') {
            return "{$slug}";
        }

        return "{$slug}/" . Str::of($orig)->snake('-')->toString();
    }

    private function guessRouteName(string $module, string $camel): string
    {
        $prefix = Str::of($module)->snake('-')->replace('_', '-')->lower()->toString();
        if ($camel === 'index') {
            return "{$prefix}.index";
        }

        return "{$prefix}." . Str::of($camel)->snake('-')->toString();
    }
}
