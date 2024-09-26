<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * @return Book[]
     */
    public function findAlldByTitleAndAuthor(string $text): array
    {
        return $this->createQueryBuilder('b')
            ->where('b.title LIKE :text')
            ->orWhere('b.author LIKE :text')
            ->setParameter('text', '%'.$text.'%')
            ->getQuery()
            ->getResult();
    }
}
