<?php

namespace App\Support\Modules\PhpAst;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as Printer;
use Throwable;

/**
 * Renames class names (Studly), methods (camelCase), injects namespaces,
 * and records a per-class method map (old->new).
 */
class MethodCamelizer
{
    public function processFile(string $absPath, string $baseModulesRoot, string $fallbackNs): array
    {
        $code = File::get($absPath);

        $parser  = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $printer = new Printer();

        try {
            $ast = $parser->parse($code);
        } catch (Throwable) {
            return [];
        }

        $namespace = $this->namespaceFromPath($absPath, $baseModulesRoot) ?? $fallbackNs;
        $methodMap = [];
        $fileName  = basename($absPath);

        $tr = new NodeTraverser();
        $tr->addVisitor(new class ($namespace, $fileName, $methodMap) extends NodeVisitorAbstract {
            private bool $hasNs = false;

            public function __construct(
                private string $ns,
                private string $origFile,
                private array &$methodMap
            ) {}

            public function enterNode(Node $node)
            {
                if ($node instanceof Node\Stmt\Namespace_) {
                    $this->hasNs = true;
                }

                if ($node instanceof Node\Stmt\Class_ && $node->name) {
                    $node->name = new Node\Identifier(self::studly((string) $node->name));
                    // CI → AdminController
                    if ($node->extends && $node->extends->toString() === 'CI_Controller') {
                        $node->extends = new Node\Name\FullyQualified('App\Http\Controllers\AdminController');
                    }
                }

                if ($node instanceof Node\Stmt\ClassMethod && $node->name) {
                    $old = (string) $node->name;
                    if ($old === '__construct') {
                        return;
                    }

                    $new = self::camel($old);
                    if ($new !== $old) {
                        $this->methodMap[$old] = $new;
                        $node->name            = new Node\Identifier($new);
                    }

                    // Attach PHPDoc with original name + file (payload can be added later)
                    $doc = "/**\n * @originalName {$old}\n * @originalFile {$this->origFile}\n */";
                    $node->setDocComment(new \PhpParser\Comment\Doc($doc));
                }
            }

            public function afterTraverse(array $nodes)
            {
                if ($this->hasNs) {
                    return $nodes;
                }

                return [new Node\Stmt\Namespace_(new Node\Name($this->ns), $nodes)];
            }

            private static function studly(string $s): string
            {
                return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $s)));
            }

            private static function camel(string $s): string
            {
                $studly = self::studly($s);

                return lcfirst($studly);
            }
        });

        $newAst = $tr->traverse($ast);

        // Pass 2: rewrite call-sites inside file (local)
        $tr2 = new NodeTraverser();
        $tr2->addVisitor(new class ($methodMap) extends NodeVisitorAbstract {
            public function __construct(private array $map) {}

            public function enterNode(Node $node)
            {
                if ($node instanceof Node\Expr\MethodCall && $node->name instanceof Node\Identifier) {
                    $old = $node->name->toString();
                    if (isset($this->map[$old])) {
                        $node->name = new Node\Identifier($this->map[$old]);
                    }
                }
                if ($node instanceof Node\Expr\StaticCall && $node->name instanceof Node\Identifier) {
                    $old = $node->name->toString();
                    if (isset($this->map[$old])) {
                        $node->name = new Node\Identifier($this->map[$old]);
                    }
                }
            }
        });
        $newAst = $tr2->traverse($newAst);

        $newCode = $printer->prettyPrintFile($newAst);
        File::put($absPath, $newCode);

        return $methodMap;
    }

    private function namespaceFromPath(string $absPath, string $baseModulesRoot): ?string
    {
        $rel = Str::after($absPath, base_path($baseModulesRoot) . DIRECTORY_SEPARATOR); // e.g. Invoices/Controllers/Foo.php
        if ($rel === $absPath) {
            return null;
        }

        $parts = collect(explode(DIRECTORY_SEPARATOR, $rel))->map(
            fn ($p) => Str::studly(preg_replace('/[^A-Za-z0-9]/', '', $p))
        );

        $parts = $parts->slice(0, max(1, $parts->count() - 1)); // drop filename
        $ns    = 'Modules\\' . $parts->implode('\\');             // Modules\Invoices\Controllers

        return trim($ns, '\\') ?: null;
    }
}
