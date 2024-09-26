<?php

namespace App\Tests\Book;

use App\Entity\Book;
use PHPUnit\Framework\TestCase;

class IsbnTest extends TestCase
{
    private Book $book;
    protected function setUp(): void
    {
        parent::setUp();

        $this->book = new Book();
        $this->book->setTitle('Title');
        $this->book->setAuthor('Author');
        $this->book->setDescription('Description');
        $this->book->setYearOfPublication(new \DateTime());
    }

    public function testIsbnValid() {

        $this->book->setIsbn('9791946341418');

        self::assertTrue($this->book->isIsbnValid());
    }

    public function testIsbnInvalid() {

        $this->book->setIsbn('9791946341411');

        self::assertFalse($this->book->isIsbnValid());
    }
}
