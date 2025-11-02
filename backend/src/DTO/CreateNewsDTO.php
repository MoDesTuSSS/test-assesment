<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateNewsDTO
{
    public function __construct(
        #[Assert\NotBlank(message: "Title is required")]
        #[Assert\Length(min: 3, max: 255)]
        public readonly string $title,

        #[Assert\NotBlank(message: "Body is required")]
        public readonly string $body,

        public readonly ?string $image = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? '',
            body: $data['body'] ?? '',
            image: $data['image'] ?? null
        );
    }
}

