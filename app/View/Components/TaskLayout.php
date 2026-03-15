<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TaskLayout extends Component
{
    /**
     * @param  string  $title       Topbar heading text
     * @param  string  $activePage  Which rail/sidebar link is highlighted: 'dashboard' | 'tasks'
     */
    public function __construct(
        public string $title      = '',
        public string $activePage = 'tasks',
    ) {}

    public function render(): View|Closure|string
    {
        return view('components.task-layout');
    }
}
