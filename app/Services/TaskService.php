<?php

namespace App\Services;

use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TaskService
{
    public function __construct(
        private TaskRepositoryInterface $repo,
        private AIService $aiService,
    ) {}

    public function getAll(array $filters = []): LengthAwarePaginator
    {
        return $this->repo->all($filters);
    }

    public function find(int $id): Task
    {
        return $this->repo->find($id);
    }

    public function store(array $data): Task
    {
        return DB::transaction(function () use ($data) {
            $task   = $this->repo->create($data);
            $aiData = $this->aiService->generateSummary($task);
            return $this->repo->update($task->id, $aiData);
        });
    }

    public function update(int $id, array $data): Task
    {
        return DB::transaction(function () use ($id, $data) {
            $task = $this->repo->update($id, $data);
            if (isset($data['title']) || isset($data['description'])) {
                $aiData = $this->aiService->generateSummary($task);
                $task   = $this->repo->update($task->id, $aiData);
            }
            return $task;
        });
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function updateStatus(int $id, string $status): Task
    {
        return $this->repo->update($id, ['status' => $status]);
    }

    public function getStats(?int $userId = null): array
    {
        return $this->repo->getStats($userId);
    }

    public function getMonthlyStats(?int $userId = null): array
    {
        return $this->repo->getMonthlyStats($userId);
    }

    public function getRecent(int $limit = 8, ?int $userId = null): Collection
    {
        return $this->repo->getRecent($limit, $userId);
    }

    public function regenerateSummary(int $id): Task
    {
        $task   = $this->repo->find($id);
        $aiData = $this->aiService->generateSummary($task);
        return $this->repo->update($id, $aiData);
    }


}
