<?php

namespace App\Http\Controllers;

use App\Services\TaskService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private TaskService $taskService
    ) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $scopeId = $user->isAdmin() ? null : $user->id;

        $monthlyData = $this->taskService->getMonthlyStats($scopeId);

        return view('dashboard', [
            'stats'       => $this->taskService->getStats($scopeId),
            'recentTasks' => $this->taskService->getRecent(8, $scopeId),
            'monthly'     => $monthlyData['counts'],
            'monthLabels' => $monthlyData['labels'],
            'isAdmin'     => $user->isAdmin(),
        ]);
    }

}
