<?php

namespace App\Application\Chirp\DTOs;

readonly class ChirpView
{
    public function __construct(
        public int $id,
        public string $title,
        public ?string $description,
        public ?string $imageUrl,
        public ?string $createdAt,
        public int $userId,
        public string $userName,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'image_url' => $this->imageUrl,
            'created_at' => $this->createdAt,
            'user' => [
                'id' => $this->userId,
                'name' => $this->userName,
            ],
        ];
    }
}
