<?php

namespace App\Tests\Unit\Entity;

use App\Entity\News;
use PHPUnit\Framework\TestCase;

class NewsTest extends TestCase
{
    public function testConstructorSetsDefaultDates(): void
    {
        $news = new News();

        $this->assertInstanceOf(\DateTimeInterface::class, $news->getCreatedAt());
        $this->assertInstanceOf(\DateTimeInterface::class, $news->getUpdatedAt());
    }

    public function testSetAndGetTitle(): void
    {
        $news = new News();
        $oldUpdatedAt = $news->getUpdatedAt();
        
        sleep(1); // Ensure time difference
        $news->setTitle('Test Title');

        $this->assertEquals('Test Title', $news->getTitle());
        $this->assertGreaterThan($oldUpdatedAt, $news->getUpdatedAt());
    }

    public function testSetAndGetBody(): void
    {
        $news = new News();
        $oldUpdatedAt = $news->getUpdatedAt();
        
        sleep(1);
        $news->setBody('Test Body');

        $this->assertEquals('Test Body', $news->getBody());
        $this->assertGreaterThan($oldUpdatedAt, $news->getUpdatedAt());
    }

    public function testSetAndGetImage(): void
    {
        $news = new News();
        $oldUpdatedAt = $news->getUpdatedAt();
        
        sleep(1);
        $news->setImage('test.jpg');

        $this->assertEquals('test.jpg', $news->getImage());
        $this->assertGreaterThan($oldUpdatedAt, $news->getUpdatedAt());
    }

    public function testImageCanBeNull(): void
    {
        $news = new News();
        $news->setImage(null);

        $this->assertNull($news->getImage());
    }

    public function testFluentInterface(): void
    {
        $news = new News();

        $result = $news
            ->setTitle('Title')
            ->setBody('Body')
            ->setImage('image.jpg');

        $this->assertSame($news, $result);
    }
}

