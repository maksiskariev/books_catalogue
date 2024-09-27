<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookCoverImageType;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

class BookController extends AbstractController
{
    #[Route('/books/{page}', name: 'app_book_list', requirements: ['page' => '\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(BookRepository $bookRepository, int $page = 1): Response
    {
        $books = $bookRepository->findAllPaginated($page);

        if (!$books->getItems()) {
            $books = null;
        }

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/book/{id}', name: 'app_book_detail')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function details(Book $book): Response
    {
        return $this->render('book/details.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/book/add', name: 'app_book_add', priority: 2)]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function add(Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BookType::class, new Book());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $em->persist($book);
            $em->flush();

            $this->addFlash('success', 'Book created!');

            return $this->redirectToRoute('app_book_detail', ['id' => $book->getId()]);
        }

        return $this->render('book/add.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/book/{id}/edit', name: 'app_book_edit')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function edit(Book $book, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $em->persist($book);
            $em->flush();

            $this->addFlash('success', 'Book updated!');

            return $this->redirectToRoute('app_book_detail', ['id' => $book->getId()]);
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form,
            'book' => $book
        ]);
    }

    #[Route('/book/{id}/delete', name: 'app_book_delete')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function delete(Book $book, EntityManagerInterface $em): Response
    {
        $em->remove($book);
        $em->flush();

        return $this->redirectToRoute('app_book_list');
    }

    #[Route('/search/{page}', name: 'app_book_search', requirements: ['page' => '\d+'])]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function search(Request $request, BookRepository $bookRepository, int $page = 1): Response
    {
        $searchText = $request->query->get('search');
        $books = $bookRepository->findAlldByTitleAndAuthor($searchText, $page);

        if (!$books->getItems()) {
            $books = null;
        }

        return $this->render('book/search.html.twig', [
            'books' => $books,
            'searchText' => $searchText,
        ]);
    }

    #[Route('/book/{id}/cover-image', name: 'app_book_cover_image')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profileImage(Book $book, Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BookCoverImageType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('coverImage')->getData();

            if ($image) {
                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('books_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                }

                $book->setCoverImage($newFilename);
                $em->persist($book);
                $em->flush();

                return $this->redirectToRoute('app_book_detail', ['id' => $book->getId()]);
            }
        }

        return $this->render('book/cover_image.html.twig', [
            'form' => $form->createView(),
            'book' => $book,
        ]);
    }
}
