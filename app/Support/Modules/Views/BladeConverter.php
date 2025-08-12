<?php

namespace App\Support\Modules\Views;

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
        if ( ! File::isDirectory($viewsDir)) {
            return;
        }

        $files = Finder::create()
            ->in($viewsDir)
            ->files()
            ->name('*.php')
            ->name('*.blade.php');

        foreach ($files as $file) {
            $src       = $file->getRealPath();
            $code      = File::get($src);
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

        $code = $this->convertEchoUrlHelpers($code);
        $code = $this->convertGenericEchos($code);

        $code = $this->convertHtmlscFirst($code);

        $code = $this->convertControls($code);
        $code = $this->convertIncludes($code);
        $code = $this->convertI18n($code);
        $code = $this->convertForms($code);

        $code = $this->convertJsTernaryToBladeIf($code);

        $code = $this->wrapRemainingPhp($code);

        $code = $this->fixDoubleBladePrefix($code);

        $code = $this->tidy($code);

        return $code;
    }

    private function convertEchoUrlHelpers(string $code): string
    {
        $siteToBlade = function (array $m): string {
            $arg = trim($m[1]);
            $uri = $this->extractLiteral($arg);
            if ($uri !== null) {
                if ($route = $this->routeMap[$uri] ?? null) {
                    return "{{ route('{$route}') }}";
                }

                return "{{ url('{$uri}') }}";
            }

            return "{{ url({$arg}) }}";
        };

        $baseToBlade = function (array $m): string {
            $arg = trim($m[1]);
            $uri = $this->extractLiteral($arg);
            if ($uri !== null) {
                $isAsset = $this->looksLikeAsset($uri);
                $helper  = $isAsset ? 'asset' : 'url';

                return "{{ {$helper}('{$uri}') }}";
            }

            return "{{ url({$arg}) }}";
        };

        $code = preg_replace_callback('/@php\s*echo\s*site_url\s*\((.*?)\)\s*;\s*@endphp/is', $siteToBlade, $code);
        $code = preg_replace_callback('/@php\s*echo\s*base_url\s*\((.*?)\)\s*;\s*@endphp/is', $baseToBlade, $code);
        $code = preg_replace_callback('/<\?php\s+echo\s+site_url\s*\((.*?)\)\s*;\s*\?>/is', $siteToBlade, $code);
        $code = preg_replace_callback('/<\?php\s+echo\s+base_url\s*\((.*?)\)\s*;\s*\?>/is', $baseToBlade, $code);
        $code = preg_replace_callback('/<\?=\s*site_url\s*\((.*?)\)\s*;?\s*\?>/is', $siteToBlade, $code);
        $code = preg_replace_callback('/<\?=\s*base_url\s*\((.*?)\)\s*;?\s*\?>/is', $baseToBlade, $code);

        return $code;
    }

    private function convertGenericEchos(string $code): string
    {
        $code = preg_replace('/<\?=\s*(.*?)\s*\?>/s', '{{ $1 }}', $code);
        $code = preg_replace('/<\?php\s+echo\s+(.*?);\s*\?>/s', '{{ $1 }}', $code);
        $code = preg_replace('/<\?php\s+print\s+(.*?);\s*\?>/s', '{{ $1 }}', $code);
        $code = preg_replace('/@php\s+echo\s+(.*?);\s+@endphp/s', '{{ $1 }}', $code);

        return $code;
    }

    private function convertHtmlscFirst(string $code): string
    {
        $code = preg_replace('/@php\s*_htmlsc\((.*?)\)\s*;?\s*@endphp/s', '{!! $1 !!}', $code);
        $code = preg_replace('/\{\{\s*htmlsc\((.*?)\)\s*\}\}/s', '{!! $1 !!}', $code);

        return $code;
    }

    private function convertControls(string $code): string
    {
        $code = preg_replace('/@php\s*if\s*\((.*?)\)\s*\{\s*@endphp/s', '@if($1)', $code);
        $code = preg_replace('/@php\s*\}\s*elseif\s*\((.*?)\)\s*\{\s*@endphp/s', '@elseif($1)', $code);
        $code = preg_replace('/@php\s*\}\s*else\s*\{\s*@endphp/s', '@else', $code);
        $code = preg_replace('/@php\s*\}\s*@endphp/s', '@endif', $code);

        $code = preg_replace('/<\?php\s+if\s*\((.*?)\)\s*:\s*\?>/s', '@if($1)', $code);
        $code = preg_replace('/<\?php\s+elseif\s*\((.*?)\)\s*:\s*\?>/s', '@elseif($1)', $code);
        $code = preg_replace('/<\?php\s+else\s*:\s*\?>/s', '@else', $code);
        $code = preg_replace('/<\?php\s+endif\s*;\s*\?>/s', '@endif', $code);

        $code = preg_replace('/@php\s*foreach\s*\((.*?)\)\s*\{\s*@endphp/s', '@foreach($1)', $code);
        $code = preg_replace('/@php\s*\}\s*endforeach\s*;\s*@endphp/s', '@endforeach', $code);

        $code = preg_replace('/<\?php\s+foreach\s*\((.*?)\)\s*:\s*\?>/s', '@foreach($1)', $code);
        $code = preg_replace('/<\?php\s+endforeach\s*;\s*\?>/s', '@endforeach', $code);

        $code = preg_replace('/<\?php\s+for\s*\((.*?)\)\s*:\s*\?>/s', '@for($1)', $code);
        $code = preg_replace('/<\?php\s+endfor\s*;\s*\?>/s', '@endfor', $code);

        $code = preg_replace('/<\?php\s+while\s*\((.*?)\)\s*:\s*\?>/s', '@while($1)', $code);
        $code = preg_replace('/<\?php\s+endwhile\s*;\s*\?>/s', '@endwhile', $code);

        return $code;
    }

    private function convertIncludes(string $code): string
    {
        $code = preg_replace('/@php\s*\$this->layout->loadView\(\s*([^)]+)\s*\)\s*;?\s*@endphp/s', '@include($1)', $code);
        $code = preg_replace('/<\?php\s*\$this->layout->loadView\(\s*([^)]+)\s*\)\s*;\s*\?>/s', '@include($1)', $code);
        $code = preg_replace('/<\?php\s*\$this->load->view\(\s*([^)]+)\s*\)\s*;\s*\?>/s', '@include($1)', $code);
        $code = preg_replace('/<\?php\s*(include|include_once|require|require_once)\s*\((.*?)\)\s*;\s*\?>/s', '@include($2)', $code);

        return $code;
    }

    private function convertI18n(string $code): string
    {
        $code = preg_replace('/@@lang\(/', '@lang(', $code);
        $code = preg_replace('/@php\s*_trans\(\s*([\'"][^\'"]+[\'"])\s*\)\s*;?\s*@endphp/', '@lang($1)', $code);
        $code = preg_replace('/@php\s*_trans\(\s*([\'"][^\'"]+[\'"])\s*\)\s*;?\s*\?>/', '@lang($1)', $code);
        $code = preg_replace('/\b_trans\(\s*([\'"][^\'"]+[\'"])\s*\)/', '@lang($1)', $code);
        $code = preg_replace('/__\(\s*([\'"][^\'"]+[\'"])\s*\)/', '@lang($1)', $code);
        $code = preg_replace('/\blang\(\s*([\'"][^\'"]+[\'"])\s*\)/', '@lang($1)', $code);

        return $code;
    }

    private function convertForms(string $code): string
    {
        $code = preg_replace_callback('/<\?php\s*echo\s*form_open\(\s*(.*?)\s*\)\s*;\s*\?>/is', function (array $m): string {
            $arg    = trim($m[1]);
            $uri    = $this->extractLiteral($arg);
            $action = $uri !== null ? "{{ url('{$uri}') }}" : "{{ url({$arg}) }}";

            return "<form action=\"{$action}\" method=\"post\">\n@csrf";
        }, $code);

        $code = preg_replace_callback('/<\?=\s*form_open\(\s*(.*?)\)\s*\?>/is', function (array $m): string {
            $arg    = trim($m[1]);
            $uri    = $this->extractLiteral($arg);
            $action = $uri !== null ? "{{ url('{$uri}') }}" : "{{ url({$arg}) }}";

            return "<form action=\"{$action}\" method=\"post\">\n@csrf";
        }, $code);

        $code = preg_replace('/<\?php\s*echo\s*form_close\(\)\s*;\s*\?>/i', '</form>', $code);
        $code = preg_replace('/<\?=\s*form_close\(\)\s*\?>/i', '</form>', $code);

        $code = preg_replace('/@php\s*_csrf_field\(\)\s*;?\s*@endphp/i', '@csrf', $code);
        $code = preg_replace('/<\?php\s*_csrf_field\(\)\s*;\s*\?>/i', '@csrf', $code);

        return $code;
    }

    private function convertJsTernaryToBladeIf(string $code): string
    {
        $code = preg_replace_callback(
            '/\{\{\s*([A-Z_][A-Z0-9_]*)\s*\?\s*([^\:}]+)\s*:\s*([^\}]+)\s*\}\}/',
            function ($m) {
                $const = trim($m[1]);
                $when  = trim($m[2]);
                $else  = trim($m[3]);

                return "@if({$const})\n{$when}\n@else\n{$else}\n@endif";
            },
            $code
        );

        $code = preg_replace_callback(
            '/@if\s*\(\s*IP_DEBUG\s*\)\s*(.*?)\s*@endif/s',
            fn ($m) => "@if(config('app.ip_debug'))\n{$m[1]}\n@endif",
            $code
        );

        return $code;
    }

    private function wrapRemainingPhp(string $code): string
    {
        if ( ! str_contains($code, '<?php')) {
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

    private function extractLiteral(string $expr): ?string
    {
        if (preg_match('/^[\'"](.+)[\'"]$/s', $expr, $m)) {
            return $m[1];
        }

        return null;
    }

    private function looksLikeAsset(string $uri): bool
    {
        $u = ltrim($uri, '/');

        return Str::startsWith($u, ['css/', 'js/', 'img/', 'images/', 'assets/', 'build/', 'vendor/', 'fonts/', 'uploads/']);
    }

    private function normalizeLineEndings(string $s): string
    {
        return str_replace(["\r\n", "\r"], "\n", $s);
    }

    private function fixDoubleBladePrefix(string $code): string
    {
        return preg_replace('/@@lang\(/', '@lang(', $code);
    }

    private function tidy(string $code): string
    {
        $code = preg_replace('/[ \t]+(\r?\n)/', '$1', $code);
        $code = preg_replace("/\n{3,}/", "\n\n", $code);

        return $code;
    }
}
