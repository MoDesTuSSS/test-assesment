<?php

namespace App\Repository;

use App\Entity\News;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<News>
 */
class NewsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, News::class);
    }

    public function save(News $news, bool $flush = false): void
    {
        $this->getEntityManager()->persist($news);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(News $news, bool $flush = false): void
    {
        $this->getEntityManager()->remove($news);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return News[] Returns an array of News objects
     */
    public function findAllOrdered(): array
    {
        return $this->createQueryBuilder('n')
            ->orderBy('n.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

