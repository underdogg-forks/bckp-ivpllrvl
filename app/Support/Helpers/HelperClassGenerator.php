<?php

namespace App\Support\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpParser\BuilderFactory;
use PhpParser\Comment\Doc;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard as Printer;

class HelperClassGenerator
{
    public function generateFromDir(string $helpersDir, string $targetDir): array
    {
        $helpersDir = base_path($helpersDir);
        $targetDir  = app_path($targetDir);
        File::ensureDirectoryExists($targetDir);

        $parser  = (new ParserFactory())->createForHostVersion();
        $printer = new Printer();
        $factory = new BuilderFactory();
        $mapping = [];

        foreach (File::files($helpersDir) as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $code = File::get($file->getRealPath());
            try {
                $ast = $parser->parse($code);
            } catch (Error) {
                continue;
            }

            $functions = [];
            foreach ($ast as $node) {
                if ($node instanceof Node\Stmt\Function_) {
                    $functions[] = $node;
                }
            }

            if ( ! $functions) {
                continue;
            }

            $className = Str::studly(pathinfo($file->getFilename(), PATHINFO_FILENAME));
            $nsName    = new Name('App\\Helpers');
            $class     = $factory->class($className)->makeFinal()->getNode();

            foreach ($functions as $fn) {
                $oldName = (string) $fn->name;
                $newName = $this->camel($oldName);

                $method = new ClassMethod($newName, [
                    'flags'      => Class_::MODIFIER_PUBLIC | Class_::MODIFIER_STATIC,
                    'params'     => $fn->params,
                    'returnType' => $fn->returnType,
                    'stmts'      => $fn->stmts ?: [],
                ]);

                $doc = "/**\n * @originalName {$oldName}\n * @originalFile {$file->getFilename()}\n */";
                $method->setDocComment(new Doc($doc));
                $class->stmts[] = $method;

                $mapping[$oldName] = [
                    'fqcn'   => "App\\Helpers\\{$className}",
                    'method' => $newName,
                    'file'   => $file->getFilename(),
                ];
            }

            $namespace = new Namespace_($nsName, [$class]);
            $newCode   = $printer->prettyPrintFile([$namespace]);

            File::put($targetDir . DIRECTORY_SEPARATOR . $className . '.php', $newCode);
        }

        return $mapping;
    }

    public function writeBackwardCompat(string $outputDir, array $mapping): void
    {
        $outputDir = app_path($outputDir);
        File::ensureDirectoryExists($outputDir);

        $blocks = [];
        foreach ($mapping as $func => $meta) {
            $fqcn     = '\\' . ltrim($meta['fqcn'], '\\');
            $method   = $meta['method'];
            $blocks[] = "if (!function_exists('{$func}')) { function {$func}(...\$args) { return {$fqcn}::{$method}(...\$args); } }";
        }

        $code = "<?php\n\n" . implode("\n", $blocks) . "\n";
        File::put($outputDir . DIRECTORY_SEPARATOR . 'helpers_bc.php', $code);
    }

    private function camel(string $s): string
    {
        $p = preg_split('/[_\-]+/', $s);

        return $p[0] . implode('', array_map('ucfirst', array_slice($p, 1)));
    }
}
