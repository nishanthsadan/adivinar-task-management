<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key', '');
    }

    /**
     * Prompt template:
     * Given a task titled "{title}" with description "{description}",
     * due on {due_date}, currently {status} with priority {priority},
     * return ONLY a JSON object with:
     *   - ai_summary: 1-2 sentence actionable summary
     *   - ai_priority: suggested priority (low|medium|high)
     */
    public function generateSummary(Task $task): array
    {
        if (empty($this->apiKey)) {
            return $this->mockResponse($task);
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(15)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model'    => 'gpt-3.5-turbo',
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a task assistant. Respond ONLY with valid JSON.'],
                        ['role' => 'user',   'content' => $this->buildPrompt($task)],
                    ],
                    'max_tokens'  => 200,
                    'temperature' => 0.4,
                ]);

            if ($response->failed()) {
                Log::warning('AIService: API failed', ['status' => $response->status()]);
                return $this->mockResponse($task);
            }

            $content = $response->json('choices.0.message.content', '{}');
            return $this->parseResponse($content, $task);

        } catch (\Throwable $e) {
            Log::error('AIService: Exception', ['message' => $e->getMessage()]);
            return $this->mockResponse($task);
        }
    }

    private function buildPrompt(Task $task): string
    {
        return sprintf(
            'Task: "%s". Description: "%s". Due: %s. Status: %s. Priority: %s. ' .
            'Return ONLY JSON with keys ai_summary and ai_priority (low|medium|high).',
            $task->title,
            $task->description ?? 'No description',
            $task->due_date?->format('Y-m-d') ?? 'no due date',
            $task->status->value,
            $task->priority->value,
        );
    }

    private function parseResponse(string $content, Task $task): array
    {
        $data = json_decode(trim($content), true);
        if (!$data || !isset($data['ai_summary'])) {
            return $this->mockResponse($task);
        }
        $allowed = ['low', 'medium', 'high'];
        return [
            'ai_summary'  => strip_tags($data['ai_summary']),
            'ai_priority' => in_array($data['ai_priority'] ?? '', $allowed)
                ? $data['ai_priority']
                : $task->priority->value,
        ];
    }

    private function mockResponse(Task $task): array
    {
        $msgs = [
            'high'   => 'This task is high priority and requires immediate attention.',
            'medium' => 'This task should be completed in a timely manner.',
            'low'    => 'This is a low-priority task. Complete when time permits.',
        ];
        return [
            'ai_summary'  => $msgs[$task->priority->value] .
                ' Task: "' . $task->title . '".',
            'ai_priority' => $task->priority->value,
        ];
    }

}
