<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setUsername('admin');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'admin'));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setUsername('johndoe');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, '12345678'));
        $manager->persist($user2);

        for ($i = 0; $i < 1000000; $i++) {
            $book = new Book();
            $book->setTitle(sprintf('Book %d', $i));
            $book->setAuthor(sprintf('Author %d', $i));
            $book->setDescription(sprintf('Description %d', $i));
            $intDate = rand(0,1727362776);
            $book->setYearOfPublication(new DateTime(date('Y-m-d', $intDate)));
            $book->setIsbn(Book::generateIsbn());
            $manager->persist($book);

            if ($i % 100 == 0) {
                $manager->flush();
                $manager->clear();
            }
        }

        $manager->flush();
        $manager->clear();
    }
}
