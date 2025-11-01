<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
    
class TicketResource extends JsonResource
{

    public string $context;

    public function __construct($resource, string $context = 'default') {
        parent::__construct($resource);
        $this->context = $context;
    }

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $data = [
            'id' => $this->id,
            'creator_id' => $this->creator_id,
            'leased_to_id' => $this->leased_to_id ?? null,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority
        ];

        if (in_array($this->context, ['store', 'index', 'show'])) {
            $data['created_at'] = optional($this->created_at)->toDateTimeString();
        } elseif ($this->context === 'update') {
            $data['updated_at'] = optional($this->updated_at)->toDateTimeString();
        }

        return $data;
    }
}
