<x-task-layout title="Task List" activePage="tasks">

    {{-- Topbar heading --}}
    <x-slot name="heading">
        <span class="tm-topbar-title">Task List</span>
    </x-slot>

    {{-- Topbar action button --}}
    <x-slot name="actions">
        @can('create', App\Models\Task::class)
            <a href="{{ route('tasks.create') }}" class="tm-btn tm-btn-primary">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                New Task
            </a>
        @endcan
    </x-slot>

    {{-- ── Flash messages ── --}}
    @if(session('success'))
        <div class="tm-flash tm-flash-ok">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="tm-flash tm-flash-err">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Filters ── --}}
    <form method="GET" action="{{ route('tasks.index') }}" class="tm-filters">

        <div class="tm-filter-search">
            <svg class="tm-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input type="text" name="search" class="tm-f-input"
                   placeholder="Search Filter Task"
                   value="{{ request('search') }}">
        </div>

        <select name="status" class="tm-f-select" onchange="this.form.submit()">
            <option value="">Status</option>
            <option value="pending"     {{ request('status') === 'pending'     ? 'selected' : '' }}>Pending</option>
            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
            <option value="completed"   {{ request('status') === 'completed'   ? 'selected' : '' }}>Completed</option>
        </select>

        @if(auth()->user()->isAdmin())
            <select name="assigned_to" class="tm-f-select" onchange="this.form.submit()">
                <option value="">All Members</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('assigned_to') == $u->id ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
        @endif

        <select name="priority" class="tm-f-select" onchange="this.form.submit()">
            <option value="">Priority</option>
            <option value="high"   {{ request('priority') === 'high'   ? 'selected' : '' }}>High</option>
            <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
            <option value="low"    {{ request('priority') === 'low'    ? 'selected' : '' }}>Low</option>
        </select>

        <button type="submit" class="tm-btn tm-btn-primary tm-btn-sm">Filter</button>

        @if(request()->hasAny(['search', 'status', 'priority', 'assigned_to']))
            <a href="{{ route('tasks.index') }}" class="tm-btn tm-btn-secondary tm-btn-sm">Clear</a>
        @endif

    </form>

    <p class="tm-filter-hint">
        {{ auth()->user()->isAdmin() ? 'All tasks' : 'Your assigned tasks' }}
        &mdash; {{ $tasks->total() }} found
    </p>

    {{-- ── Task grid ── --}}
    <div class="tm-grid">
        @forelse($tasks as $task)
            @php
                $spClass = match($task->status->value) {
                    'in_progress' => 'tm-spill-in_progress',
                    'completed'   => 'tm-spill-completed',
                    default       => 'tm-spill-pending',
                };
                $spLabel = match($task->status->value) {
                    'in_progress' => 'In Progress',
                    'completed'   => 'Completed',
                    default       => 'Pending',
                };
                $prClass = match($task->priority->value) {
                    'high'   => 'tm-badge-high',
                    'medium' => 'tm-badge-medium',
                    default  => 'tm-badge-low',
                };
                $aipClass = match($task->ai_priority?->value) {
                    'high'   => 'tm-card-aip-high',
                    'medium' => 'tm-card-aip-medium',
                    default  => 'tm-card-aip-low',
                };
            @endphp

            <div class="tm-card">
                <div class="tm-card-top">
                <span class="tm-spill {{ $spClass }}">
                    <span class="tm-dot"></span>{{ $spLabel }}
                </span>
                    <a href="{{ route('tasks.show', $task) }}" class="tm-card-menu" title="View task">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <circle cx="4"  cy="10" r="1.5"/>
                            <circle cx="10" cy="10" r="1.5"/>
                            <circle cx="16" cy="10" r="1.5"/>
                        </svg>
                    </a>
                </div>

                <div class="tm-card-title">{{ $task->title }}</div>

                <div class="tm-badges">
                    <span class="tm-badge tm-badge-neutral">Status</span>
                    <span class="tm-badge {{ $prClass }}">Priority {{ ucfirst($task->priority->value) }}</span>
                </div>

                @if($task->description)
                    <div class="tm-card-desc">{{ $task->description }}</div>
                @endif

                @if($task->ai_summary)
                    <div class="tm-card-ai">{{ Str::limit($task->ai_summary, 90) }}</div>
                @endif

                <div class="tm-card-meta">
                    @if($task->assignedUser)
                        <div class="tm-card-meta-row">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Assignee: {{ $task->assignedUser->name }}
                        </div>
                    @endif
                    @if($task->due_date)
                        <div class="tm-card-meta-row">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Due: {{ $task->due_date->format('Y-m-d') }}
                        </div>
                    @endif
                </div>

                @if($task->ai_priority)
                    <div class="tm-card-aip {{ $aipClass }}">{{ ucfirst($task->ai_priority->value) }}</div>
                @endif

                <div class="tm-card-actions">
                    @can('update', $task)
                        <a href="{{ route('tasks.edit', $task) }}" class="tm-btn tm-btn-secondary tm-btn-sm">Edit</a>
                    @endcan
                    <a href="{{ route('tasks.show', $task) }}" class="tm-btn tm-btn-primary tm-btn-sm">View</a>
                </div>
            </div>

        @empty
            <div class="tm-grid-empty">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p>No tasks found. Adjust your filters or create a new task.</p>
            </div>
        @endforelse
    </div>

    <div class="tm-pagination">
        {{ $tasks->withQueryString()->links() }}
    </div>

</x-task-layout>
