<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title . ' — ' : '' }}{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
</head>
<body class="tm-page">
<div class="tm-shell">

    {{-- ════════════════════════════════════════════
         LEFT ICON RAIL
    ════════════════════════════════════════════ --}}
    <nav class="tm-rail">
        <a href="{{ route('dashboard') }}" class="tm-rail-logo">T</a>

        <a href="{{ route('dashboard') }}"
           class="tm-rail-btn {{ $activePage === 'dashboard' ? 'active' : '' }}"
           title="Dashboard">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
        </a>

        <a href="{{ route('tasks.index') }}"
           class="tm-rail-btn {{ $activePage === 'tasks' ? 'active' : '' }}"
           title="Tasks">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
        </a>

        <div class="tm-rail-spacer"></div>

        <a href="{{ route('profile.edit') }}" class="tm-rail-btn" title="Profile">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </a>
    </nav>

    {{-- ════════════════════════════════════════════
         MAIN COLUMN
    ════════════════════════════════════════════ --}}
    <div class="tm-main-col">

        {{-- ── Topbar ── --}}
        <div class="tm-topbar">
            <div class="tm-topbar-left">
                {{ $heading ?? '' }}
            </div>
            <div class="tm-topbar-right">
                {{ $actions ?? '' }}
            </div>
        </div>

        <div class="tm-body-row">

            {{-- ── Page content ── --}}
            <div class="tm-content">
                {{ $slot }}
            </div>

            {{-- ════════════════════════════════════════════
                 RIGHT SIDEBAR
            ════════════════════════════════════════════ --}}
            <aside class="tm-sidebar">

                {{-- User info card --}}
                <div class="tm-sb-user">
                    <div class="tm-sb-user-top">
                        <div class="tm-sb-avatar">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="tm-sb-name">{{ auth()->user()->name }}</div>
                            <div class="tm-sb-role">{{ ucfirst(auth()->user()->role->value) }}</div>
                        </div>
                    </div>

                    <nav class="tm-sb-nav">
                        <a href="{{ route('tasks.index') }}"
                           class="tm-sb-link {{ $activePage === 'tasks' ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Tasks
                            <span class="tm-sb-count">{{ $sidebarStats['total'] }}</span>
                        </a>

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('dashboard') }}"
                               class="tm-sb-link {{ $activePage === 'dashboard' ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                Users

                            </a>
                        @endif

                        <a href="{{ route('logout') }}" class="tm-sb-link"
                           onclick="event.preventDefault(); document.getElementById('tm-logout-form').submit();">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </a>
                        <form id="tm-logout-form" action="{{ route('logout') }}" method="POST" hidden>
                            @csrf
                        </form>
                    </nav>
                </div>

                {{-- Stats widget --}}
                <div class="tm-sb-widget">
                    <div class="tm-sb-widget-title">Monthly Summary</div>
                    <div class="tm-sb-stats-row">
                        <div class="tm-sb-stat">
                            <div class="tm-sb-stat-val">{{ $sidebarStats['total'] }}</div>
                            <div class="tm-sb-stat-lbl">Total</div>
                        </div>
                        <div class="tm-sb-stat">
                            <div class="tm-sb-stat-val tm-sb-stat-val-green">{{ $sidebarStats['completed'] }}</div>
                            <div class="tm-sb-stat-lbl">Done</div>
                        </div>
                        <div class="tm-sb-stat">
                            <div class="tm-sb-stat-val tm-sb-stat-val-red">{{ $sidebarStats['high'] }}</div>
                            <div class="tm-sb-stat-lbl">High</div>
                        </div>
                    </div>
                </div>

                {{-- Monthly chart --}}
                <div class="tm-sb-widget">
                    <div class="tm-sb-widget-title">Monthly Task Completion</div>
                    <canvas id="tm-sidebar-chart" height="140"></canvas>
                </div>

            </aside>

        </div>
    </div>
</div>

{{-- Sidebar chart --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Chart(document.getElementById('tm-sidebar-chart'), {
            type: 'bar',
            data: {
                labels:   @json($sidebarMonthly['labels']),
                datasets: [{
                    data:                @json($sidebarMonthly['counts']),
                    backgroundColor:     '#2563eb',
                    hoverBackgroundColor:'#3b82f6',
                    borderRadius:        5,
                    borderSkipped:       false,
                }]
            },
            options: {
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        ticks:  { color: '#3d4f63', font: { size: 10 } },
                        grid:   { display: false },
                        border: { display: false },
                    },
                    y: {
                        ticks:  { color: '#3d4f63', font: { size: 10 }, stepSize: 1 },
                        grid:   { color: '#1f2d42' },
                        border: { display: false },
                        beginAtZero: true,
                    }
                }
            }
        });
    });
</script>

{{-- Per-page scripts slot --}}
{{ $scripts ?? '' }}

</body>
</html>
