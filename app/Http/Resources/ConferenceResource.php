<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'link' => $this->link,
            'active' => $this->active,
            'created_at' => $this->created_at->translatedFormat('d F Y'),
            'deadline_at' => $this->deadline_at->translatedFormat('d F Y, H:i'),
            'scheduled_date' => $this->scheduled_date->translatedFormat('d F Y'),
        ];
    }
}
