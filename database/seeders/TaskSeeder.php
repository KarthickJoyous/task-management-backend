<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Factories\TaskFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaskFactory::new()->count(10)->create();
    }
}
