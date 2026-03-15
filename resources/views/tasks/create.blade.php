<x-task-layout title="Create Task" activePage="tasks">

    <x-slot name="heading">
        <a href="{{ route('tasks.index') }}" class="tm-back-link">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Tasks
        </a>
        <span class="tm-breadcrumb-sep">/</span>
        <span class="tm-page-title">Create New Task</span>
    </x-slot>

    <div class="tm-form-page">
        <div class="tm-form-card">
            <form method="POST" action="{{ route('tasks.store') }}">
                @csrf

                <div class="tm-form-group">
                    <label for="title" class="tm-form-label">
                        Title <span class="tm-form-required">*</span>
                    </label>
                    <input type="text" id="title" name="title"
                           class="tm-form-control @error('title') tm-form-control-error @enderror"
                           placeholder="Enter task title"
                           value="{{ old('title') }}"
                           autofocus>
                    @error('title')
                    <p class="tm-form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="tm-form-group">
                    <label for="description" class="tm-form-label">Description</label>
                    <textarea id="description" name="description"
                              class="tm-form-control"
                              placeholder="Describe the task in detail...">{{ old('description') }}</textarea>
                </div>

                <div class="tm-form-grid">
                    <div class="tm-form-group">
                        <label for="priority" class="tm-form-label">
                            Priority <span class="tm-form-required">*</span>
                        </label>
                        <select id="priority" name="priority" class="tm-form-select">
                            @foreach(\App\Enums\TaskPriority::cases() as $p)
                                <option value="{{ $p->value }}"
                                    {{ old('priority', 'medium') === $p->value ? 'selected' : '' }}>
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
                                    {{ old('status', 'pending') === $s->value ? 'selected' : '' }}>
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
                               value="{{ old('due_date') }}">
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
                                    {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
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
                    <a href="{{ route('tasks.index') }}" class="tm-btn tm-btn-secondary">Cancel</a>
                    <button type="submit" class="tm-btn tm-btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4v16m8-8H4"/>
                        </svg>
                        Create Task
                    </button>
                </div>

            </form>
        </div>
    </div>

</x-task-layout>
