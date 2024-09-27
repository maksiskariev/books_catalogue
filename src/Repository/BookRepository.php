<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginator)
    {
        parent::__construct($registry, Book::class);
    }

    public function findAlldByTitleAndAuthor(string $text, int $page)
    {
        $query = $this->createQueryBuilder('b');
        $searchTerms = explode(' ', $text);

        foreach ($searchTerms as $key => $term) {
            $query
                ->orWhere('b.title LIKE :text')
                ->orWhere('b.author LIKE :text')
                ->setParameter('text', '%'.trim($term).'%');
        }

        $query->getQuery();

        $pagination = $this->paginator->paginate($query, $page, 40);

        return $pagination;
    }

    public function findAllPaginated(int $page)
    {
        $query = $this->createQueryBuilder('b')->getQuery();

        $pagination = $this->paginator->paginate($query, $page, 40);

        return $pagination;
    }
}
