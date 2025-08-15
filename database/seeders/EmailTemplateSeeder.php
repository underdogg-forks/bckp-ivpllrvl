<?php

namespace Database\Seeders;

class EmailTemplateSeeder extends AbstractSeeder
{
    public function run(): void
    {
        $this->progress('Creating {{ model }}', function () {
            // {{ fill the model name here model }}::factory(10)->create();
        });
    }
}
