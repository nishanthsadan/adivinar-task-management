<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'priority'      => $this->priority->value,
            'status'        => $this->status->value,
            'due_date'      => $this->due_date?->format('Y-m-d'),
            'assigned_to'   => $this->assigned_to,
            'assigned_user' => $this->whenLoaded('assignedUser', fn() => [
                'id'   => $this->assignedUser->id,
                'name' => $this->assignedUser->name,
            ]),
            'ai_summary'    => $this->ai_summary,
            'ai_priority'   => $this->ai_priority?->value,
            'created_at'    => $this->created_at->toISOString(),
            'updated_at'    => $this->updated_at->toISOString(),
        ];

    }
}
