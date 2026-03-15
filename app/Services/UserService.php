<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function __construct(
        private UserRepositoryInterface $repo
    ) {}

    public function getAll(): Collection
    {
        return $this->repo->all();
    }

    public function find(int $id): User
    {
        return $this->repo->find($id);
    }
}
