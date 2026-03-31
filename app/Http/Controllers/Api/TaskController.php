<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\TaskIndexRequest;
use App\Http\Requests\TaskReportRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $taskService
    ) {
    }

    public function index(TaskIndexRequest $request): JsonResponse
    {
        $tasks = $this->taskService->list($request->validated());

        if ($tasks->total() === 0) {
            return response()->json([
                'message' => 'No tasks found.',
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'per_page' => $request->integer('per_page', 15),
                    'total' => 0,
                    'last_page' => 1,
                ],
            ], 200);
        }

        return response()->json([
            'message' => 'Tasks retrieved successfully.',
            'data' => TaskResource::collection($tasks->items()),
            'meta' => [
                'current_page' => $tasks->currentPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
                'last_page' => $tasks->lastPage(),
            ],
            'links' => [
                'first' => $tasks->url(1),
                'last' => $tasks->url($tasks->lastPage()),
                'prev' => $tasks->previousPageUrl(),
                'next' => $tasks->nextPageUrl(),
            ],
        ], 200);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = $this->taskService->create($request->validated());

        return response()->json([
            'message' => 'Task created successfully.',
            'data' => new TaskResource($task),
        ], 201);
    }

    public function updateStatus(UpdateTaskStatusRequest $request, Task $task): JsonResponse
    {
        $task = $this->taskService->updateStatus(
            $task,
            $request->validated()['status']
        );

        return response()->json([
            'message' => 'Task status updated successfully.',
            'data' => new TaskResource($task),
        ], 200);
    }

    public function destroy(Task $task): JsonResponse
    {
        $this->taskService->delete($task);

        return response()->json([
            'message' => 'Task deleted successfully.',
        ], 200);
    }

    public function report(TaskReportRequest $request): JsonResponse
    {
        $report = $this->taskService->dailyReport(
            $request->validated()['date']
        );

        return response()->json([
            'message' => 'Daily task report generated successfully.',
            'data' => $report,
        ], 200);
    }
}