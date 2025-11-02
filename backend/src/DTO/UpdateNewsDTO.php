<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateNewsDTO
{
    public function __construct(
        #[Assert\Length(min: 3, max: 255)]
        public readonly ?string $title = null,

        public readonly ?string $body = null,

        public readonly ?string $image = null
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'] ?? null,
            body: $data['body'] ?? null,
            image: $data['image'] ?? null
        );
    }

    public function hasTitle(): bool
    {
        return $this->title !== null;
    }

    public function hasBody(): bool
    {
        return $this->body !== null;
    }

    public function hasImage(): bool
    {
        return isset($this->image);
    }
}

