<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        private TaskService $taskService,
        private UserService $userService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Task::class);

        $filters = $request->only(['status', 'priority', 'search', 'assigned_to']);

        if (!$request->user()->isAdmin()) {
            $filters['assigned_to'] = $request->user()->id;
        }

        return view('tasks.index', [
            'tasks'   => $this->taskService->getAll($filters),
            'filters' => $filters,
            'users'   => $this->userService->getAll(),
        ]);

    }

    /**
     * Show the form for creating a new resource.
     */

    public function create()
    {
        $this->authorize('create', Task::class);

        return view('tasks.create', [
            'users' => $this->userService->getAll(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = $this->taskService->store($request->validated());

        return redirect()
            ->route('tasks.show', $task)
            ->with('success', 'Task created with AI analysis.');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('view', $task);

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('update', $task);

        return view('tasks.edit', [
            'task'  => $task,
            'users' => $this->userService->getAll(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('update', $task);
        $this->taskService->update($id, $request->validated());

        return redirect()
            ->route('tasks.show', $id)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('delete', $task);
        $this->taskService->delete($id);

        return redirect()
            ->route('tasks.index')
            ->with('success', 'Task deleted.');
    }

    public function regenerateAI(int $id)
    {
        $task = $this->taskService->find($id);
        $this->authorize('view', $task);
        $this->taskService->regenerateSummary($id);

        return back()->with('success', 'AI summary regenerated successfully.');
    }
}
