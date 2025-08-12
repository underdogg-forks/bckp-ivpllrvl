<?php

namespace App\Support\Modules\PhpAst;

use Illuminate\Support\Facades\File;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as Printer;
use Symfony\Component\Finder\Finder;
use Throwable;

class CallSiteRewriter
{
    /**
     * Rewrites all method calls in a tree according to flat map old->new.
     *
     * @param array<string,string> $flatMap
     */
    public function rewriteTree(string $absDir, array $flatMap): void
    {
        if ( ! File::isDirectory($absDir)) {
            return;
        }

        $parser  = (new ParserFactory())->createForHostVersion();
        $printer = new Printer();

        $files = (new Finder())->files()->in($absDir)->name('*.php');
        foreach ($files as $file) {
            $code = File::get($file->getRealPath());
            try {
                $ast = $parser->parse($code);
            } catch (Throwable) {
                continue;
            }

            $tr = new NodeTraverser();
            $tr->addVisitor(new class ($flatMap) extends NodeVisitorAbstract {
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
            $newAst  = $tr->traverse($ast);
            $newCode = $printer->prettyPrintFile($newAst);
            File::put($file->getRealPath(), $newCode);
        }
    }
}
