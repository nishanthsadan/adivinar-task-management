<x-task-layout title="Edit Task" activePage="tasks">

    <x-slot name="heading">
        <a href="{{ route('tasks.show', $task) }}" class="tm-back-link">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Task
        </a>
        <span class="tm-breadcrumb-sep">/</span>
        <span class="tm-page-title">Edit: {{ Str::limit($task->title, 40) }}</span>
    </x-slot>

    <x-slot name="actions">
        @can('delete', $task)
            <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                  onsubmit="return confirm('Delete this task?')">
                @csrf @method('DELETE')
                <button type="submit" class="tm-btn tm-btn-danger">Delete Task</button>
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

    <div class="tm-form-page">
        <div class="tm-form-card">
            <form method="POST" action="{{ route('tasks.update', $task) }}">
                @csrf
                @method('PUT')

                <div class="tm-form-group">
                    <label for="title" class="tm-form-label">
                        Title <span class="tm-form-required">*</span>
                    </label>
                    <input type="text" id="title" name="title"
                           class="tm-form-control @error('title') tm-form-control-error @enderror"
                           placeholder="Enter task title"
                           value="{{ old('title', $task->title) }}"
                           autofocus>
                    @error('title')
                    <p class="tm-form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="tm-form-group">
                    <label for="description" class="tm-form-label">Description</label>
                    <textarea id="description" name="description"
                              class="tm-form-control"
                              placeholder="Describe the task in detail...">{{ old('description', $task->description) }}</textarea>
                </div>

                <div class="tm-form-grid">
                    <div class="tm-form-group">
                        <label for="priority" class="tm-form-label">
                            Priority <span class="tm-form-required">*</span>
                        </label>
                        <select id="priority" name="priority" class="tm-form-select">
                            @foreach(\App\Enums\TaskPriority::cases() as $p)
                                <option value="{{ $p->value }}"
                                    {{ old('priority', $task->priority->value) === $p->value ? 'selected' : '' }}>
                                    {{ ucfirst($p->value) }}
                                </option>
                            @endforeach
                        </select>
                        @error('priority')
                        <p class="tm-form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="tm-form-group">
                        <label for="status" class="tm-form-label">
                            Status <span class="tm-form-required">*</span>
                        </label>
                        <select id="status" name="status" class="tm-form-select">
                            @foreach(\App\Enums\TaskStatus::cases() as $s)
                                <option value="{{ $s->value }}"
                                    {{ old('status', $task->status->value) === $s->value ? 'selected' : '' }}>
                                    {{ ucfirst(str_replace('_', ' ', $s->value)) }}
                                </option>
                            @endforeach
                        </select>
                        @error('status')
                        <p class="tm-form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="tm-form-grid">
                    <div class="tm-form-group">
                        <label for="due_date" class="tm-form-label">Due Date</label>
                        <input type="date" id="due_date" name="due_date"
                               class="tm-form-control"
                               value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                        @error('due_date')
                        <p class="tm-form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="tm-form-group">
                        <label for="assigned_to" class="tm-form-label">Assign To</label>
                        <select id="assigned_to" name="assigned_to" class="tm-form-select">
                            <option value="">— Unassigned —</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('assigned_to')
                        <p class="tm-form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="tm-form-actions">
                    <a href="{{ route('tasks.show', $task) }}" class="tm-btn tm-btn-secondary">Cancel</a>
                    <button type="submit" class="tm-btn tm-btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                </div>

            </form>
        </div>
    </div>

</x-task-layout>
