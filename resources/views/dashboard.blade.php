<x-task-layout title="Dashboard" activePage="dashboard">

    <x-slot name="heading">
        <span class="tm-topbar-title">Dashboard</span>
    </x-slot>

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

    @if(session('success'))
        <div class="tm-flash tm-flash-ok">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Scope label ── --}}
    <p class="tm-filter-hint">
        @if($isAdmin)
            Showing all tasks across all users
        @else
            Showing your assigned tasks only
        @endif
    </p>

    {{-- ── Stat cards ── --}}
    <div class="db-stat-grid">

        <div class="db-stat-card">
            <div class="db-stat-icon db-stat-icon-blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
            <div class="db-stat-body">
                <div class="db-stat-val">{{ $stats['total'] }}</div>
                <div class="db-stat-lbl">Total Tasks</div>
            </div>
        </div>

        <div class="db-stat-card">
            <div class="db-stat-icon db-stat-icon-green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="db-stat-body">
                <div class="db-stat-val db-stat-val-green">{{ $stats['completed'] }}</div>
                <div class="db-stat-lbl">Completed</div>
            </div>
        </div>

        <div class="db-stat-card">
            <div class="db-stat-icon db-stat-icon-yellow">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="db-stat-body">
                <div class="db-stat-val db-stat-val-yellow">{{ $stats['pending'] }}</div>
                <div class="db-stat-lbl">Pending</div>
            </div>
        </div>

        <div class="db-stat-card">
            <div class="db-stat-icon db-stat-icon-purple">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <div class="db-stat-body">
                <div class="db-stat-val db-stat-val-purple">{{ $stats['in_progress'] }}</div>
                <div class="db-stat-lbl">In Progress</div>
            </div>
        </div>

        <div class="db-stat-card">
            <div class="db-stat-icon db-stat-icon-red">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="db-stat-body">
                <div class="db-stat-val db-stat-val-red">{{ $stats['high'] }}</div>
                <div class="db-stat-lbl">High Priority</div>
            </div>
        </div>

    </div>

    {{-- ── Charts row ── --}}
    <div class="db-charts-row">

        <div class="db-chart-card">
            <div class="db-chart-header">
                <span class="db-chart-title">
                    Monthly Task Overview
                    @unless($isAdmin)
                        <span class="db-chart-scope">(Your Tasks)</span>
                    @endunless
                </span>
            </div>
            <canvas id="db-bar-chart" height="220"></canvas>
        </div>

        <div class="db-chart-card db-chart-card-sm">
            <div class="db-chart-header">
                <span class="db-chart-title">Status Breakdown</span>
            </div>
            <canvas id="db-doughnut-chart" height="220"></canvas>
            <div class="db-donut-legend">
                <div class="db-donut-legend-item">
                    <span class="db-donut-dot db-donut-dot-blue"></span>Pending
                </div>
                <div class="db-donut-legend-item">
                    <span class="db-donut-dot db-donut-dot-purple"></span>In Progress
                </div>
                <div class="db-donut-legend-item">
                    <span class="db-donut-dot db-donut-dot-green"></span>Completed
                </div>
            </div>
        </div>

    </div>

    {{-- ── Recent tasks table ── --}}
    <div class="db-table-card">
        <div class="db-table-header">
            <span class="db-chart-title">
                @if($isAdmin)
                    Recent Tasks
                @else
                    Your Recent Tasks
                @endif
            </span>
            <a href="{{ route('tasks.index') }}" class="tm-btn tm-btn-secondary tm-btn-sm">
                View All
            </a>
        </div>

        <div class="db-table-wrap">
            <table class="db-table">
                <thead>
                <tr>
                    <th class="db-th">Title</th>
                    <th class="db-th">Priority</th>
                    <th class="db-th">Status</th>
                    @if($isAdmin)
                        <th class="db-th">Assigned To</th>
                    @endif
                    <th class="db-th">Due Date</th>
                    <th class="db-th db-th-right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($recentTasks as $task)
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
                    <tr class="db-tr">
                        <td class="db-td">
                            <a href="{{ route('tasks.show', $task) }}" class="db-task-link">
                                {{ Str::limit($task->title, 40) }}
                            </a>
                        </td>
                        <td class="db-td">
                            <span class="tm-badge {{ $prClass }}">
                                {{ ucfirst($task->priority->value) }}
                            </span>
                        </td>
                        <td class="db-td">
                            <span class="tm-spill {{ $spClass }}">
                                <span class="tm-dot"></span>{{ $spLabel }}
                            </span>
                        </td>
                        @if($isAdmin)
                            <td class="db-td db-td-muted">
                                {{ $task->assignedUser?->name ?? '—' }}
                            </td>
                        @endif
                        <td class="db-td db-td-muted">
                            {{ $task->due_date?->format('M d, Y') ?? '—' }}
                        </td>
                        <td class="db-td db-td-right">
                            <a href="{{ route('tasks.show', $task) }}"
                               class="tm-btn tm-btn-primary tm-btn-sm">
                                View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? 6 : 5 }}" class="db-td db-td-empty">
                            @if($isAdmin)
                                No tasks yet.
                                <a href="{{ route('tasks.create') }}" class="db-empty-link">Create one</a>
                            @else
                                No tasks assigned to you yet.
                            @endif
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Dashboard chart scripts --}}
    <x-slot name="scripts">
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                new Chart(document.getElementById('db-bar-chart'), {
                    type: 'bar',
                    data: {
                        labels:   @json($monthLabels),
                        datasets: [{
                            data:                @json($monthly),
                            backgroundColor:     '#2563eb',
                            hoverBackgroundColor:'#3b82f6',
                            borderRadius:        5,
                            borderSkipped:       false,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: {
                                ticks:  { color: '#3d4f63', font: { size: 11 } },
                                grid:   { display: false },
                                border: { display: false },
                            },
                            y: {
                                ticks:  { color: '#3d4f63', font: { size: 11 }, stepSize: 1 },
                                grid:   { color: '#1f2d42' },
                                border: { display: false },
                                beginAtZero: true,
                            }
                        }
                    }
                });

                new Chart(document.getElementById('db-doughnut-chart'), {
                    type: 'doughnut',
                    data: {
                        labels:   ['Pending', 'In Progress', 'Completed'],
                        datasets: [{
                            data:            [{{ $stats['pending'] }}, {{ $stats['in_progress'] }}, {{ $stats['completed'] }}],
                            backgroundColor: ['#2563eb', '#a78bfa', '#22c55e'],
                            borderColor:     '#151c28',
                            borderWidth:     3,
                            hoverOffset:     6,
                        }]
                    },
                    options: {
                        responsive: true,
                        cutout: '68%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => ' ' + ctx.label + ': ' + ctx.parsed
                                }
                            }
                        }
                    }
                });

            });
        </script>
    </x-slot>

</x-task-layout>
