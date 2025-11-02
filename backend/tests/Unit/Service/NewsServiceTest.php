<?php

namespace App\Tests\Unit\Service;

use App\DTO\CreateNewsDTO;
use App\DTO\UpdateNewsDTO;
use App\Entity\News;
use App\Repository\NewsRepository;
use App\Service\NewsService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class NewsServiceTest extends TestCase
{
    private EntityManagerInterface $entityManager;
    private NewsRepository $repository;
    private LoggerInterface $logger;
    private NewsService $service;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = $this->createMock(NewsRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->service = new NewsService(
            $this->entityManager,
            $this->repository,
            $this->logger
        );
    }

    public function testFindAll(): void
    {
        $news1 = new News();
        $news2 = new News();
        $expected = [$news1, $news2];

        $this->repository
            ->expects($this->once())
            ->method('findAllOrdered')
            ->willReturn($expected);

        $result = $this->service->findAll();

        $this->assertSame($expected, $result);
    }

    public function testFindById(): void
    {
        $news = new News();
        $id = 1;

        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with($id)
            ->willReturn($news);

        $result = $this->service->findById($id);

        $this->assertSame($news, $result);
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $this->repository
            ->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $result = $this->service->findById(999);

        $this->assertNull($result);
    }

    public function testCreate(): void
    {
        $dto = new CreateNewsDTO(
            title: 'Test Title',
            body: 'Test Body',
            image: 'test.jpg'
        );

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($this->isInstanceOf(News::class));

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->logger
            ->expects($this->exactly(2))
            ->method('info');

        $result = $this->service->create($dto);

        $this->assertInstanceOf(News::class, $result);
        $this->assertEquals('Test Title', $result->getTitle());
        $this->assertEquals('Test Body', $result->getBody());
        $this->assertEquals('test.jpg', $result->getImage());
    }

    public function testUpdate(): void
    {
        $news = new News();
        $news->setTitle('Old Title');
        $news->setBody('Old Body');

        $dto = new UpdateNewsDTO(
            title: 'New Title',
            body: 'New Body'
        );

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->logger
            ->expects($this->exactly(2))
            ->method('info');

        $result = $this->service->update($news, $dto);

        $this->assertEquals('New Title', $result->getTitle());
        $this->assertEquals('New Body', $result->getBody());
    }

    public function testUpdateOnlyTitle(): void
    {
        $news = new News();
        $news->setTitle('Old Title');
        $news->setBody('Old Body');

        $dto = new UpdateNewsDTO(title: 'New Title');

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $result = $this->service->update($news, $dto);

        $this->assertEquals('New Title', $result->getTitle());
        $this->assertEquals('Old Body', $result->getBody());
    }

    public function testDelete(): void
    {
        $news = new News();
        $news->setTitle('Test');
        $news->setBody('Test');

        $this->entityManager
            ->expects($this->once())
            ->method('remove')
            ->with($news);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->logger
            ->expects($this->exactly(2))
            ->method('info');

        $this->service->delete($news);
    }
}

