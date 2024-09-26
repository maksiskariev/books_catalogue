<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\Index(name: 'idx_book', columns: ['title'])]
#[ORM\Index(name: 'idx_author', columns: ['author'])]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $author = null;

    #[ORM\Column(length: 500)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $yearOfPublication = null;

    #[ORM\Column(length: 13)]
    private ?string $isbn = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $coverImage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(string $author): static
    {
        $this->author = $author;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getYearOfPublication(): ?\DateTimeInterface
    {
        return $this->yearOfPublication;
    }

    public function setYearOfPublication(\DateTimeInterface $yearOfPublication): static
    {
        $this->yearOfPublication = $yearOfPublication;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): static
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(?string $coverImage): static
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public static function generateIsbn() : string
    {
        $prefix = (rand(0, 1) == 0) ? '979' : '978';
        $registrationGroup = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        $registrant = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
        $publication = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);

        $isbnWithoutChecksum = $prefix . $registrationGroup . $registrant . $publication;
        $checksum = self::calculateChecksum($isbnWithoutChecksum);

        return $isbnWithoutChecksum . $checksum;
    }

    #[Assert\IsTrue(message: 'ISBN is invalid.')]
    public function isIsbnValid() : bool
    {
        $isbnWithoutChecksum = substr_replace($this->isbn, '', -1);
        $checksum = self::calculateChecksum($isbnWithoutChecksum);

        return $checksum == $this->isbn[12];
    }

    private static function calculateChecksum(string $isbnWithoutChecksum) : int
    {
        $checksum = 0;

        for ($i = 0; $i < 12; $i++) {
            $checksum += (int) $isbnWithoutChecksum[$i] * ((($i % 2) == 1) ? 3 : 1);
        }

        return (10 - ($checksum % 10)) % 10;
    }
}
