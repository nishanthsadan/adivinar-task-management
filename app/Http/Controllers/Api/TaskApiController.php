<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;


class TaskApiController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);
        $tasks = $this->taskService->getAll(
            $request->only(['status', 'priority', 'search'])
        );
        return TaskResource::collection($tasks);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Task::class);
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'required|in:low,medium,high',
            'status'      => 'required|in:pending,in_progress,completed',
            'due_date'    => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
        ]);
        $task = $this->taskService->store($validated);
        return new TaskResource($task);
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);
        $task = $this->taskService->updateStatus($id, $request->status);
        $this->authorize('update', $task);
        return new TaskResource($task);
    }

    public function aiSummary(int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('view', $task);
        return response()->json([
            'ai_summary'  => $task->ai_summary,
            'ai_priority' => $task->ai_priority?->value,
        ]);
    }

}
