<?php

namespace App\Models;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'priority', 'status',
        'due_date', 'assigned_to', 'ai_summary', 'ai_priority',
    ];

    protected function casts(): array
    {
        return [
            'priority'    => TaskPriority::class,
            'status'      => TaskStatus::class,
            'ai_priority' => TaskPriority::class,
            'due_date'    => 'date',
        ];
    }

    // Relationship: task belongs to a user
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Query scope for filtering (used by Repository)
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['status']      ?? null,
                fn($q, $v) => $q->where('status', $v))
            ->when($filters['priority']    ?? null,
                fn($q, $v) => $q->where('priority', $v))
            ->when($filters['assigned_to'] ?? null,
                fn($q, $v) => $q->where('assigned_to', $v))
            ->when($filters['search']      ?? null,
                fn($q, $v) => $q->where('title', 'like', "%{$v}%"));
    }

}
