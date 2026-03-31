<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function create(array $data): Task
    {
        return Task::create([
            'title' => $data['title'],
            'due_date' => $data['due_date'],
            'priority' => $data['priority'],
            'status' => Task::STATUS_PENDING,
        ]);
    }

    public function list(array $filters): LengthAwarePaginator
    {
        $perPage = $filters['per_page'] ?? 15;

        return Task::query()
            ->filterStatus($filters['status'] ?? null)
            ->sortByBusinessPriority()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function updateStatus(Task $task, string $newStatus): Task
    {
        $allowedTransitions = [
            Task::STATUS_PENDING => Task::STATUS_IN_PROGRESS,
            Task::STATUS_IN_PROGRESS => Task::STATUS_DONE,
            Task::STATUS_DONE => null,
        ];

        $expectedNextStatus = $allowedTransitions[$task->status] ?? null;

        if ($expectedNextStatus === null) {
            throw ValidationException::withMessages([
                'status' => ['Completed tasks cannot change status.'],
            ]);
        }

        if ($newStatus !== $expectedNextStatus) {
            throw ValidationException::withMessages([
                'status' => [
                    sprintf(
                        'Invalid status transition. Allowed transition is only from %s to %s.',
                        $task->status,
                        $expectedNextStatus
                    ),
                ],
            ]);
        }

        $task->update([
            'status' => $newStatus,
        ]);

        return $task->refresh();
    }

    public function delete(Task $task): void
    {
        if ($task->status !== Task::STATUS_DONE) {
            throw new AuthorizationException('Only tasks with status done can be deleted.');
        }

        $task->delete();
    }

    public function dailyReport(string $date): array
    {
        $priorities = [
            Task::PRIORITY_HIGH,
            Task::PRIORITY_MEDIUM,
            Task::PRIORITY_LOW,
        ];

        $statuses = [
            Task::STATUS_PENDING,
            Task::STATUS_IN_PROGRESS,
            Task::STATUS_DONE,
        ];

        $tasks = Task::query()
            ->whereDate('due_date', $date)
            ->get(['priority', 'status']);

        $summary = [];

        foreach ($priorities as $priority) {
            foreach ($statuses as $status) {
                $summary[$priority][$status] = $tasks
                    ->where('priority', $priority)
                    ->where('status', $status)
                    ->count();
            }
        }

        return [
            'date' => $date,
            'summary' => $summary,
        ];
    }
}