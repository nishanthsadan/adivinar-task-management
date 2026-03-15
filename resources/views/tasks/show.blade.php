<x-task-layout title="{{ $task->title }}" activePage="tasks">

    <x-slot name="heading">
        <a href="{{ route('tasks.index') }}" class="tm-back-link">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Tasks
        </a>
        <span class="tm-breadcrumb-sep">/</span>
        <span class="tm-page-title">{{ Str::limit($task->title, 45) }}</span>
    </x-slot>

    <x-slot name="actions">
        @can('update', $task)
            <a href="{{ route('tasks.edit', $task) }}" class="tm-btn tm-btn-secondary">Edit</a>
        @endcan
        @can('delete', $task)
            <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                  onsubmit="return confirm('Delete this task?')">
                @csrf @method('DELETE')
                <button type="submit" class="tm-btn tm-btn-danger">Delete</button>
            </form>
        @endcan
    </x-slot>

    @if(session('success'))
        <div class="tm-flash tm-flash-ok">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Task detail card --}}
    <div class="tm-detail-card">
        <div class="tm-detail-title">{{ $task->title }}</div>

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
        @endphp

        <div class="tm-detail-badges">
            <span class="tm-spill {{ $spClass }}"><span class="tm-dot"></span>{{ $spLabel }}</span>
            <span class="tm-badge {{ $prClass }}">Priority: {{ ucfirst($task->priority->value) }}</span>
        </div>

        <div class="tm-meta-grid">
            <div class="tm-meta-chip">
                <div class="tm-meta-chip-label">Assigned To</div>
                <div class="tm-meta-chip-value">{{ $task->assignedUser?->name ?? 'Unassigned' }}</div>
            </div>
            <div class="tm-meta-chip">
                <div class="tm-meta-chip-label">Due Date</div>
                <div class="tm-meta-chip-value">{{ $task->due_date?->format('M d, Y') ?? 'No due date' }}</div>
            </div>
            <div class="tm-meta-chip">
                <div class="tm-meta-chip-label">Created</div>
                <div class="tm-meta-chip-value">{{ $task->created_at->format('M d, Y') }}</div>
            </div>
            <div class="tm-meta-chip">
                <div class="tm-meta-chip-label">Updated</div>
                <div class="tm-meta-chip-value">{{ $task->updated_at->format('M d, Y') }}</div>
            </div>
        </div>

        @if($task->description)
            <div class="tm-section-head">Description</div>
            <p class="tm-desc-text">{{ $task->description }}</p>
        @endif
    </div>

    {{-- AI analysis card --}}
    <div class="tm-ai-card">
        <div class="tm-ai-header">
            <div class="tm-ai-label">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.346A3.75 3.75 0 0113 18.75h-2a3.75 3.75 0 01-2.696-1.14l-.347-.346z"/>
                </svg>
                AI Analysis
            </div>
            <form method="POST" action="{{ route('tasks.regenerate-ai', $task->id) }}">
                @csrf
                <button type="submit" class="tm-btn-regen">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Regenerate
                </button>
            </form>
        </div>

        @if($task->ai_summary)
            <p class="tm-ai-body">{{ $task->ai_summary }}</p>
            @if($task->ai_priority)
                @php
                    $aipClass = match($task->ai_priority->value) {
                        'high'   => 'tm-badge-high',
                        'medium' => 'tm-badge-medium',
                        default  => 'tm-badge-low',
                    };
                @endphp
                <div class="tm-ai-priority-row">
                    <span class="tm-ai-priority-label">AI Suggested Priority:</span>
                    <span class="tm-badge {{ $aipClass }}">{{ ucfirst($task->ai_priority->value) }}</span>
                </div>
            @endif
        @else
            <p class="tm-ai-body tm-ai-body-empty">
                No AI analysis yet. Click Regenerate to generate one.
            </p>
        @endif
    </div>

</x-task-layout>
