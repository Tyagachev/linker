<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatisticsResource extends JsonResource
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
            'comment' => $this->comment ?? null,
            'deadline_at' => $this->deadline_at,
            'scheduled_date' => $this->scheduled_date->translatedFormat('d F Y'),
            'dead' => $this->deadline_at->translatedFormat('d F Y, H:i'),
            'salons' => $this->salons
        ];
    }
}
