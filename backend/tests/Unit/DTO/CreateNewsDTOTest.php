<?php

namespace App\Tests\Unit\DTO;

use App\DTO\CreateNewsDTO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class CreateNewsDTOTest extends TestCase
{
    private $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testCreateFromValidArray(): void
    {
        $data = [
            'title' => 'Test Title',
            'body' => 'Test Body',
            'image' => 'test.jpg'
        ];

        $dto = CreateNewsDTO::fromArray($data);

        $this->assertEquals('Test Title', $dto->title);
        $this->assertEquals('Test Body', $dto->body);
        $this->assertEquals('test.jpg', $dto->image);
    }

    public function testCreateFromArrayWithoutImage(): void
    {
        $data = [
            'title' => 'Test Title',
            'body' => 'Test Body'
        ];

        $dto = CreateNewsDTO::fromArray($data);

        $this->assertEquals('Test Title', $dto->title);
        $this->assertEquals('Test Body', $dto->body);
        $this->assertNull($dto->image);
    }

    public function testValidationFailsWithEmptyTitle(): void
    {
        $dto = new CreateNewsDTO('', 'Test Body');

        $violations = $this->validator->validate($dto);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testValidationFailsWithShortTitle(): void
    {
        $dto = new CreateNewsDTO('AB', 'Test Body');

        $violations = $this->validator->validate($dto);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testValidationFailsWithLongTitle(): void
    {
        $dto = new CreateNewsDTO(str_repeat('A', 256), 'Test Body');

        $violations = $this->validator->validate($dto);

        $this->assertGreaterThan(0, count($violations));
    }

    public function testValidationSucceedsWithValidData(): void
    {
        $dto = new CreateNewsDTO('Valid Title', 'Valid Body');

        $violations = $this->validator->validate($dto);

        $this->assertEquals(0, count($violations));
    }
}

