<?php

namespace App\Repositories\Eloquent;

use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TaskRepository implements TaskRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator
    {
        return Task::query()
            ->with('assignedUser')
            ->filter($filters)
            ->latest()
            ->paginate(10)
            ->withQueryString();
    }

    public function find(int $id): Task
    {
        return Task::with('assignedUser')->findOrFail($id);
    }

    public function create(array $data): Task
    {
        $task = Task::create($data);
        $this->clearCache();
        return $task;
    }

    public function update(int $id, array $data): Task
    {
        $task = $this->find($id);
        $task->update($data);
        $this->clearCache();
        return $task->fresh();
    }

    public function delete(int $id): bool
    {
        $task = $this->find($id);
        $this->clearCache();
        return (bool) $task->delete();
    }

    public function getStats(?int $userId = null): array
    {
        $cacheKey = $userId ? "task_stats_user_{$userId}" : 'task_stats';

        return Cache::remember($cacheKey, 60, function () use ($userId) {
            $base = Task::query()->when($userId, fn($q) => $q->where('assigned_to', $userId));

            return [
                'total'       => (clone $base)->count(),
                'completed'   => (clone $base)->where('status', 'completed')->count(),
                'pending'     => (clone $base)->where('status', 'pending')->count(),
                'in_progress' => (clone $base)->where('status', 'in_progress')->count(),
                'high'        => (clone $base)->where('priority', 'high')->count(),
            ];
        });
    }

    public function getMonthlyStats(?int $userId = null): array
    {
        $cacheKey = $userId ? "task_monthly_stats_user_{$userId}" : 'task_monthly_stats';

        return Cache::remember($cacheKey, 60, function () use ($userId) {
            $counts = [];
            $labels = [];

            for ($i = 4; $i >= 0; $i--) {
                $date = now()->subMonths($i);

                $counts[] = Task::query()
                    ->when($userId, fn($q) => $q->where('assigned_to', $userId))
                    ->whereYear('created_at',  $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();

                $labels[] = $date->format('M');
            }

            return ['counts' => $counts, 'labels' => $labels];
        });
    }

    public function getRecent(int $limit = 8, ?int $userId = null): Collection
    {
        return Task::query()
            ->with('assignedUser')
            ->when($userId, fn($q) => $q->where('assigned_to', $userId))
            ->latest()
            ->limit($limit)
            ->get();
    }

    private function clearCache(): void
    {
        Cache::forget('task_stats');
        Cache::forget('task_monthly_stats');

        $userIds = \App\Models\User::pluck('id');
        foreach ($userIds as $id) {
            Cache::forget("task_stats_user_{$id}");
            Cache::forget("task_monthly_stats_user_{$id}");
        }
    }

}
