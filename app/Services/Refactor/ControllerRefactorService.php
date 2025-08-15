<?php

namespace App\Services\Refactor;

use App\Services\Refactor\Traits\LogsRefactorChanges;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class ControllerRefactorService
{
    use LogsRefactorChanges;

    public function refactor(string $modulesPath, bool $dry, string $log): int
    {
        $count  = 0;
        $finder = (new Finder())->files()->in($modulesPath)->name('*Controller.php');
        foreach ($finder as $file) {
            $path = $file->getRealPath();
            $c    = File::get($path);
            $orig = $c;

            $c = $this->replaceBufferToView($path, $c);
            $c = $this->replaceLoadViewToView($path, $c);
            $c = $this->renameModelIdentifiers($c);
            $c = $this->rewriteInlineDbToEloquent($c);

            if ($this->putIfChanged($path, $c, $dry, $log, 'Controller updated')) {
                $count++;
            }
        }

        return $count;
    }

    private function moduleNameFromPath(string $path): string
    {
        $parts = preg_split('#[/\\\\]+#', $path);
        $i     = array_search('Modules', $parts);
        if ($i === false) {
            return '';
        }

        return mb_strtolower($parts[$i + 1] ?? '');
    }

    private function replaceBufferToView(string $path, string $c): string
    {
        $module = $this->moduleNameFromPath($path);

        return preg_replace_callback(
            "/->buffer\(\s*'content'\s*,\s*'([^']+)'\s*\)/",
            function ($m) use ($module) {
                $view = str_replace('/', '.', $m[1]);

                return "->with('content', view('{$module}::{$view}'))";
            },
            $c
        );
    }

    private function replaceLoadViewToView(string $path, string $c): string
    {
        $module = $this->moduleNameFromPath($path);
        $c      = preg_replace_callback(
            "/->load->view\(\s*'([^']+)'\s*\)/",
            function ($m) use ($module) {
                $view = str_replace('/', '.', $m[1]);

                return "return view('{$module}::{$view}')";
            },
            $c
        );
        $c = preg_replace_callback(
            "/->load->view\(\s*'([^']+)'\s*,\s*([^)]+)\)/",
            function ($m) use ($module) {
                $view = str_replace('/', '.', $m[1]);

                return "return view('{$module}::{$view}', {$m[2]})";
            },
            $c
        );

        return $c;
    }

    private function renameModelIdentifiers(string $c): string
    {
        $c = preg_replace('/\$(this->)?mdl_([A-Za-z0-9_]+)/', '$1srv_$2', $c);

        return $c;
    }

    private function rewriteInlineDbToEloquent(string $c): string
    {
        // Simple get operations
        $c = preg_replace_callback("/\\{$this->db}->get\\(\\s*'([A-Za-z0-9_]+)'\\s*\\)/", function ($m) {
            $model = '\\App\\Models\\' . Str::studly($m[1]);

            return "{$model}::query()->get()";
        }, $c);

        // Where + get operations
        $c = preg_replace_callback("/\\{$this->db}->where\\(\\s*'([A-Za-z0-9_]+)'\\s*,\\s*([^\\)]+)\\)\\s*->\\s*get\\(\\s*'([A-Za-z0-9_]+)'\\s*\\)/", function ($m) {
            $col   = $m[1];
            $val   = trim($m[2]);
            $table = $m[3];
            $model = '\\App\\Models\\' . Str::studly($table);

            return "{$model}::query()->where('{$col}', {$val})->get()";
        }, $c);

        // Insert operations
        $c = preg_replace_callback("/\\{$this->db}->insert\\(\\s*'([A-Za-z0-9_]+)'\\s*,\\s*([^\\)]+)\\)/", function ($m) {
            $model = '\\App\\Models\\' . Str::studly($m[1]);
            $data  = trim($m[2]);

            return "{$model}::query()->create({$data})";
        }, $c);

        // Update operations
        $c = preg_replace_callback("/\\{$this->db}->update\\(\\s*'([A-Za-z0-9_]+)'\\s*,\\s*([^,]+)\\s*,\\s*([^\\)]+)\\)/", function ($m) {
            $model = '\\App\\Models\\' . Str::studly($m[1]);
            $data  = trim($m[2]);
            $where = trim($m[3]);

            return "{$model}::query()->where(\${$where})->update({$data})";
        }, $c);

        // Delete operations
        $c = preg_replace_callback("/\\{$this->db}->delete\\(\\s*'([A-Za-z0-9_]+)'\\s*,\\s*([^\\)]+)\\)/", function ($m) {
            $model = '\\App\\Models\\' . Str::studly($m[1]);
            $where = trim($m[2]);

            return "{$model}::query()->where(\${$where})->delete()";
        }, $c);

        return $c;
    }
}
