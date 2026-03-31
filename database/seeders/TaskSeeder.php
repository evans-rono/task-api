<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        Task::insert([
            [
                'title' => 'Prepare API contract',
                'due_date' => now()->toDateString(),
                'priority' => Task::PRIORITY_HIGH,
                'status' => Task::STATUS_PENDING,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Implement task listing',
                'due_date' => now()->addDay()->toDateString(),
                'priority' => Task::PRIORITY_MEDIUM,
                'status' => Task::STATUS_IN_PROGRESS,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Deploy to Railway',
                'due_date' => now()->addDays(2)->toDateString(),
                'priority' => Task::PRIORITY_LOW,
                'status' => Task::STATUS_DONE,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}