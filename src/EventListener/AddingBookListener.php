<?php

namespace App\EventListener;

use App\Entity\Book;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Psr\Log\LoggerInterface;

final class AddingBookListener
{
    public function __construct(private LoggerInterface $booksLogger)
    {
    }

    public function postPersist(PostPersistEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof Book) {
            return;
        }

        $this->booksLogger->info('Book created', ['entity' => [
            'id' => $entity->getId(),
            'title' => $entity->getTitle(),
            'author' => $entity->getAuthor(),
            'description' => $entity->getDescription(),
            'yearOfPublication' => $entity->getYearOfPublication(),
            'isbn' => $entity->getIsbn(),
        ]]);
    }
}
