<?php

namespace App\Providers;

use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        /*
         * Share sidebar stats and monthly chart data with the task-layout component.
         */
        View::composer('components.task-layout', function (\Illuminate\View\View $view) {
            if (!Auth::check()) {
                return;
            }

            $user = Auth::user();
            $taskService = app(TaskService::class);

            $scopeId = $user->isAdmin() ? null : $user->id;

            $view->with('sidebarStats',   $taskService->getStats($scopeId));
            $view->with('sidebarMonthly', $taskService->getMonthlyStats($scopeId));
        });
    }
}
