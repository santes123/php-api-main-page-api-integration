<?php

namespace App\Domain;

class Task
{
    public function __construct(
        public ?int $id,
        public int $userId,
        public string $title,
        public ?string $description,
        public string $status,
        public string $createdAt,
        public string $updatedAt
    ) {}
}
