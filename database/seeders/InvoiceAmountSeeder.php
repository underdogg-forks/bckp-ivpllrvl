<?php

namespace Database\Seeders;

use Database\Seeders\AbstractSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InvoiceAmountSeeder extends AbstractSeeder
{
    public function run(): void
    {
        $this->progress('Creating {{ model }}', function () {
            // {{ fill the model name here model }}::factory(10)->create();
        });
    }
}
