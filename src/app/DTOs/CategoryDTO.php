<?php

namespace App\DTOs;

readonly class CategoryDTO
{
    public function __construct(
        public string $name,
        public readonly ?int $created_by,
        public ?string $color_code,
        public ?string $description,
        public bool $is_active = FALSE,
    ) {}

    /**
     * Factory method to create DTO from validated request data.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            color_code: $data['color_code'],
            description: $data['description'] ?? NULL,
            created_by: $data['created_by'] ?? NULL,
            is_active: $data['is_active'] ?? false,
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'color_code' => $this->color_code,
            'description' => $this->description,
            'created_by' => $this->created_by,
            'is_active' => $this->is_active,
        ];
    }
}