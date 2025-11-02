<?php

namespace App\Service;

use App\DTO\CreateNewsDTO;
use App\DTO\UpdateNewsDTO;
use App\Entity\News;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class NewsService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly NewsRepository $newsRepository,
        private readonly LoggerInterface $logger
    ) {
    }

    /**
     * @return News[]
     */
    public function findAll(): array
    {
        return $this->newsRepository->findAllOrdered();
    }

    public function findById(int $id): ?News
    {
        return $this->newsRepository->find($id);
    }

    public function create(CreateNewsDTO $dto): News
    {
        $this->logger->info('Creating news', ['title' => $dto->title]);

        $news = new News();
        $news->setTitle($dto->title);
        $news->setBody($dto->body);
        $news->setImage($dto->image);

        $this->entityManager->persist($news);
        $this->entityManager->flush();

        $this->logger->info('News created', ['id' => $news->getId()]);

        return $news;
    }

    public function update(News $news, UpdateNewsDTO $dto): News
    {
        $this->logger->info('Updating news', ['id' => $news->getId()]);

        if ($dto->hasTitle()) {
            $news->setTitle($dto->title);
        }

        if ($dto->hasBody()) {
            $news->setBody($dto->body);
        }

        if ($dto->hasImage()) {
            $news->setImage($dto->image);
        }

        $this->entityManager->flush();

        $this->logger->info('News updated', ['id' => $news->getId()]);

        return $news;
    }

    public function delete(News $news): void
    {
        $id = $news->getId();
        $this->logger->info('Deleting news', ['id' => $id]);

        // Delete associated image file if exists
        if ($news->getImage() && str_starts_with($news->getImage(), '/uploads/')) {
            $imagePath = $this->entityManager->getConfiguration()
                ->getProjectDir() . '/public' . $news->getImage();
            
            if (file_exists($imagePath)) {
                @unlink($imagePath);
            }
        }

        $this->entityManager->remove($news);
        $this->entityManager->flush();

        $this->logger->info('News deleted', ['id' => $id]);
    }
}

