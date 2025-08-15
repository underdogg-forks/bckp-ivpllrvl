<?php

namespace App\Services\Refactor;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

class BladeConverter
{
    private array $routeMap;

    public function __construct(array $routeMap = [])
    {
        $this->routeMap = $routeMap;
    }

    public function convertDir(string $viewsDir, bool $inPlace = true): void
    {
        if (!File::isDirectory($viewsDir)) {
            return;
        }

        $files = Finder::create()
            ->in($viewsDir)
            ->files()
            ->name('*.php')
            ->name('*.blade.php');

        foreach ($files as $file) {
            $src = $file->getRealPath();
            $code = File::get($src);
            $converted = $this->convertContent($code);

            if (Str::endsWith($src, '.blade.php')) {
                File::put($src, $converted);
                continue;
            }

            if ($inPlace) {
                File::put($src, $converted);
                $dest = preg_replace('/\.php$/', '.blade.php', $src);
                if ($dest && $dest !== $src) {
                    @rename($src, $dest);
                }
            } else {
                $dest = preg_replace('/\.php$/', '.blade.php', $src);
                if ($dest) {
                    File::put($dest, $converted);
                }
            }
        }
    }

    public function convertContent(string $code): string
    {
        $code = $this->normalizeLineEndings($code);

        // Step 0: Remove unwanted declarations
        $code = $this->removeNamespaceDeclaration($code);

        // Step 1: Handle all language and htmlsc conversions first.
        $code = $this->convertI18n($code);
        $code = $this->convertHtmlscFirst($code);

        // Step 2: Convert complex control structures.
        $code = $this->convertSwitchStatements($code);
        $code = $this->convertControlBlocks($code);

        // Step 3: Convert PHP includes and other functions to Blade includes.
        $code = $this->convertIncludes($code);
        $code = $this->convertGenericEchos($code);

        // Step 4: Final cleanup
        $code = $this->convertTernaryToIf($code);
        $code = $this->wrapRemainingPhp($code);
        $code = $this->fixDanglingPhpTags($code);
        $code = $this->fixDoubleBladePrefix($code);
        $code = $this->tidy($code);

        return $code;
    }

    private function removeNamespaceDeclaration(string $code): string
    {
        return preg_replace('/@php\s+namespace\s+.*?\s*@endphp/', '', $code);
    }

    private function convertGenericEchos(string $code): string
    {
        // Convert CI-style echos
        $code = preg_replace('/<\?=\s*(.*?)\s*\?>/s', '{{ $1 }}', $code);
        $code = preg_replace('/<\?php\s+echo\s+(.*?);\s*\?>/s', '{{ $1 }}', $code);
        $code = preg_replace('/<\?php\s+print\s+(.*?);\s*\?>/s', '{{ $1 }}', $code);
        $code = preg_replace('/@php\s+echo\s+(.*?);\s+@endphp/s', '{{ $1 }}', $code);

        // New: Convert nl2br(htmlsc(...))
        $code = preg_replace('/\{\{\s*nl2br\s*\(\s*htmlsc\s*\((.*?)\)\s*\)\s*\}\}/s', '{{ nl2br(e($1)) }}', $code);

        return $code;
    }

    private function convertHtmlscFirst(string $code): string
    {
        // htmlsc() to e()
        $code = preg_replace('/@php\s*_htmlsc\((.*?)\)\s*;?\s*@endphp/s', '{!! $1 !!}', $code);
        $code = preg_replace('/\{\{\s*htmlsc\((.*?)\)\s*\}\}/s', '{!! $1 !!}', $code);
        $code = preg_replace('/_htmlsc\((.*?)\)/s', 'e($1)', $code);

        return $code;
    }

    private function convertControlBlocks(string $code): string
    {
        // New: Convert selected attribute within @if
        $code = preg_replace('/@if\((.*?)\)\s*\{\s*echo\s+\'selected="selected"\';\s*\}/s', '@if($1) selected="selected" @endif', $code);

        // New: Convert dangling PHP/Blade foreach/if blocks to Blade
        $code = preg_replace('/@php\s*foreach\s*\((.*?)\)\s*\{\s*@endphp/s', '@foreach($1)', $code);
        $code = preg_replace('/@php\s*if\s*\((.*?)\)\s*\{\s*@endphp/s', '@if($1)', $code);
        $code = preg_replace('/@php\s*\}\s*endforeach\s*@endphp/s', '@endforeach', $code);

        // Convert full PHP blocks to Blade
        $code = preg_replace('/<\?php\s+if\s*\((.*?)\)\s*:\s*\?>/s', '@if($1)', $code);
        $code = preg_replace('/<\?php\s+elseif\s*\((.*?)\)\s*:\s*\?>/s', '@elseif($1)', $code);
        $code = preg_replace('/<\?php\s+else\s*:\s*\?>/s', '@else', $code);
        $code = preg_replace('/<\?php\s+endif\s*;\s*\?>/s', '@endif', $code);

        $code = preg_replace('/<\?php\s+foreach\s*\((.*?)\)\s*:\s*\?>/s', '@foreach($1)', $code);
        $code = preg_replace('/<\?php\s+endforeach\s*;\s*\?>/s', '@endforeach', $code);

        $code = preg_replace('/<\?php\s+for\s*\((.*?)\)\s*:\s*\?>/s', '@for($1)', $code);
        $code = preg_replace('/<\?php\s+endfor\s*;\s*\?>/s', '@endfor', $code);

        $code = preg_replace('/<\?php\s+while\s*\((.*?)\)\s*:\s*\?>/s', '@while($1)', $code);
        $code = preg_replace('/<\?php\s+endwhile\s*;\s*\?>/s', '@endwhile', $code);

        // This regex specifically handles the @php if(condition) { @endphp ... @endif block
        $code = preg_replace('/@php\s*if\s*\((.*?)\)\s*\{\s*@endphp/', '@if($1)', $code);
        $code = preg_replace('/@php\s*\}\s*@endphp\s*@endif/', '@endif', $code);

        return $code;
    }

    private function convertSwitchStatements(string $code): string
    {
        $code = preg_replace_callback(
            '/@php\s*switch\s*\((.*?)\)\s*\{\s*.*?\}\s*@endphp/s',
            function ($matches) {
                $content = preg_replace('/case\s*([^:]+):/s', '@case($1):', $matches[0]);
                $content = str_replace('break;', '@break', $content);
                $content = str_replace('default:', '@default', $content);
                return "@switch({$matches[1]})\n" . $content . "\n@endswitch";
            },
            $code
        );

        return $code;
    }

    private function convertIncludes(string $code): string
    {
        // Convert CI includes with potential typos
        $code = preg_replace('/<\?php\s*\$this->layout->loadView\(\s*([^)]+)\s*\)\s*;\s*\?>/s', '@include($1)', $code);
        $code = preg_replace('/<\?php\s*\$this->load->view\(\s*([^)]+)\s*\)\s*;\s*\?>/s', '@include($1)', $code);

        // New: Handle the `$this->layout->loadView(...) !!}` typo
        $code = preg_replace('/@php\s*\$this->layout->loadView\(\s*([^)]+)\)\s*!!}/s', '@include($1)', $code);
        $code = preg_replace_callback(
            "/@include\('([^']*)'\)/",
            function ($matches) {
                $viewPath = Str::replace('/', '.', $matches[1]);
                return "@include('{$viewPath}')";
            },
            $code
        );

        return $code;
    }

    private function convertI18n(string $code): string
    {
        $code = preg_replace('/@php\s*_trans\(\s*([\'"][^\'"]+[\'"])\s*\)\s*;?\s*@endphp/', '@lang($1)', $code);
        $code = preg_replace('/\b_trans\(\s*([\'"][^\'"]+[\'"])\s*\)/', '@lang($1)', $code);
        $code = preg_replace('/__\(\s*([\'"][^\'"]+[\'"])\s*\)/', '@lang($1)', $code);
        $code = preg_replace('/\blang\(\s*([\'"][^\'"]+[\'"])\s*\)/', '@lang($1)', $code);

        $code = str_replace('@trans', '@lang', $code);

        return $code;
    }

    private function wrapRemainingPhp(string $code): string
    {
        $code = preg_replace('/@php\s*\/\/\s*([^\r\n]+)\s*if\s*\((.*?)\)\s*\{\s*@endphp/', "<!-- $1 -->\n@if($2)", $code);

        if (!str_contains($code, '<?php')) {
            return $code;
        }

        $result = preg_replace_callback(
            '/<\?php\s*(.*?)\s*\?>/s',
            static function (array $m): string {
                return '@php ' . $m[1] . ' @endphp';
            },
            $code
        );

        return is_string($result) ? $result : $code;
    }

    private function fixDanglingPhpTags(string $code): string
    {
        // New: Handle "}" followed by comment and @endphp
        $code = preg_replace('/\}\s*\/\/\s*endif\s*@endphp/', '@endif', $code);
        $code = preg_replace('/\}\s*\/\/\s*End\s*foreach\s*@endphp/', '@endforeach', $code);

        $code = preg_replace('/<\?php\s*\}\s*@endphp/s', '@endif', $code);
        $code = preg_replace('/<\?php\s*endforeach\s*;\s*\?>/s', '@endforeach', $code);
        $code = preg_replace('/<\?php\s*endif\s*;\s*\?>/s', '@endif', $code);
        $code = preg_replace('/<\?php\s*endfor\s*;\s*\?>/s', '@endfor', $code);

        return $code;
    }

    private function convertTernaryToIf(string $code): string
    {
        return $code;
    }

    private function fixDoubleBladePrefix(string $code): string
    {
        return preg_replace('/@@lang\(/', '@lang(', $code);
    }

    private function normalizeLineEndings(string $s): string
    {
        return str_replace(["\r\n", "\r"], "\n", $s);
    }

    private function tidy(string $code): string
    {
        $code = preg_replace('/[ \t]+(\r?\n)/', '$1', $code);
        $code = preg_replace("/\n{3,}/", "\n\n", $code);

        return $code;
    }
}
