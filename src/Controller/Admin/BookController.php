<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/book')]
#[IsGranted('ROLE_ADMIN')]
class BookController extends AbstractController
{
    private $entityManager;
    private $bookRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        BookRepository $bookRepository
    ) {
        $this->entityManager = $entityManager;
        $this->bookRepository = $bookRepository;
    }

    #[Route('/', name: 'admin_book_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/book/index.html.twig', [
            'books' => $this->bookRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($book);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le livre a été créé avec succès.');
            return $this->redirectToRoute('admin_book_index');
        }

        return $this->render('admin/book/new.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('admin/book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'Le livre a été modifié avec succès.');
            return $this->redirectToRoute('admin_book_index');
        }

        return $this->render('admin/book/edit.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($book);
            $this->entityManager->flush();

            $this->addFlash('success', 'Le livre a été supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_book_index');
    }
}
