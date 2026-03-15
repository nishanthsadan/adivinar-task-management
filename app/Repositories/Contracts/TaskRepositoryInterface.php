<?php

namespace App\Repositories\Contracts;

use App\Models\Task;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TaskRepositoryInterface
{
    public function all(array $filters = []): LengthAwarePaginator;
    public function find(int $id): Task;
    public function create(array $data): Task;
    public function update(int $id, array $data): Task;
    public function delete(int $id): bool;
    public function getStats(?int $userId = null): array;
    public function getMonthlyStats(?int $userId = null): array;
    public function getRecent(int $limit = 8, ?int $userId = null): Collection;

}
