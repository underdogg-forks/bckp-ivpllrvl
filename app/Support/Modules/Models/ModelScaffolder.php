<?php

namespace App\Support\Modules\Models;

use Illuminate\Support\Facades\Artisan;

class ModelScaffolder
{
    public function make(string $name, bool $resource = false): void
    {
        $args = [
            'name'        => $name,
            '--migration' => true,
            '--factory'   => true,
            '--seeder'    => true,
        ];
        if ($resource) {
            $args['--controller'] = true;
            $args['--resource']   = true;
        }
        Artisan::call('make:model', $args);
    }
}
