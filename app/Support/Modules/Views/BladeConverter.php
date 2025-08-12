<?php

namespace App\Support\Modules\Views;

use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class BladeConverter
{
    public function convertDir(string $viewsDir, bool $inPlace = true): void
    {
        if ( ! File::isDirectory($viewsDir)) {
            return;
        }

        $files = (new Finder())->files()->in($viewsDir)->name('*.php');
        foreach ($files as $file) {
            $src  = $file->getRealPath();
            $code = File::get($src);

            // Echoes
            $code = preg_replace('/<\?= (.*?) \?>/s', '{{ $1 }}', $code);
            $code = preg_replace('/<\?php echo (.*?); \?>/s', '{{ $1 }}', $code);
            $code = preg_replace('/<\?php print (.*?); \?>/s', '{{ $1 }}', $code);

            // Control structures
            $code = preg_replace('/<\?php if\s*\((.*?)\): \?>/s', '@if($1)', $code);
            $code = preg_replace('/<\?php elseif\s*\((.*?)\): \?>/s', '@elseif($1)', $code);
            $code = preg_replace('/<\?php else: \?>/s', '@else', $code);
            $code = preg_replace('/<\?php endif; \?>/s', '@endif', $code);

            $code = preg_replace('/<\?php foreach\s*\((.*?)\): \?>/s', '@foreach($1)', $code);
            $code = preg_replace('/<\?php endforeach; \?>/s', '@endforeach', $code);

            $code = preg_replace('/<\?php for\s*\((.*?)\): \?>/s', '@for($1)', $code);
            $code = preg_replace('/<\?php endfor; \?>/s', '@endfor', $code);

            $code = preg_replace('/<\?php while\s*\((.*?)\): \?>/s', '@while($1)', $code);
            $code = preg_replace('/<\?php endwhile; \?>/s', '@endwhile', $code);

            // Includes (CI → Blade)
            $code = preg_replace('/<\?php \$this->load->view\((.*?)\);\s*\?>/s', '@include($1)', $code);
            $code = preg_replace('/<\?php (include|include_once|require|require_once)\s*\((.*?)\);\s*\?>/s', '@include($2)', $code);

            // Localization: remove __(), use @lang()
            $code = preg_replace('/__\([\'"](.*?)[\'"]\)/', '@lang(\'$1\')', $code);

            // Any remaining PHP → @php
            $code = preg_replace('/<\?php (.*?) \?>/s', '@php $1 @endphp', $code);

            if ( ! $inPlace) {
                $dest = preg_replace('/\.php$/', '.blade.php', $src);
                File::put($dest, $code);
                continue;
            }

            File::put($src, $code);
            $dest = preg_replace('/\.php$/', '.blade.php', $src);
            if ($dest !== $src) {
                @rename($src, $dest);
            }
        }
    }
}
