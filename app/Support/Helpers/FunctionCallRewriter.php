<?php

namespace App\Support\Helpers;

use Illuminate\Support\Facades\File;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as Printer;
use Symfony\Component\Finder\Finder;
use Throwable;

class FunctionCallRewriter
{
    public function rewriteTree(array $roots, array $functionMap): void
    {
        $parser  = (new ParserFactory())->createForHostVersion();
        $printer = new Printer();

        foreach ($roots as $root) {
            $abs = base_path($root);
            if ( ! File::isDirectory($abs)) {
                continue;
            }

            $files = Finder::create()->files()->in($abs)->name('*.php');
            foreach ($files as $file) {
                $code = File::get($file->getRealPath());
                try {
                    $ast = $parser->parse($code);
                } catch (Throwable) {
                    continue;
                }

                $tr = new NodeTraverser();
                $tr->addVisitor(new class ($functionMap) extends NodeVisitorAbstract {
                    public function __construct(private array $map) {}

                    public function enterNode(Node $node)
                    {
                        if ($node instanceof Node\Expr\FuncCall && $node->name instanceof Node\Name) {
                            $name = $node->name->toString();
                            if (isset($this->map[$name])) {
                                $meta = $this->map[$name];
                                $node = new Node\Expr\StaticCall(
                                    new Node\Name\FullyQualified($meta['fqcn']),
                                    $meta['method'],
                                    $node->args
                                );

                                return $node;
                            }
                        }
                    }
                });

                $newAst  = $tr->traverse($ast);
                $newCode = $printer->prettyPrintFile($newAst);
                File::put($file->getRealPath(), $newCode);
            }
        }
    }
}
