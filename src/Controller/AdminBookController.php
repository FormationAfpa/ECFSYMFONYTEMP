<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookLoan;
use App\Entity\BookComment;
use App\Form\BookType;
use App\Form\LoanCommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/books')]
#[IsGranted('ROLE_ADMIN')]
class AdminBookController extends AbstractController
{
    #[Route('', name: 'admin_book_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $books = $entityManager->getRepository(Book::class)->findAll();
        $overdueBooks = $entityManager->getRepository(BookLoan::class)->findOverdueLoans();

        return $this->render('admin/book/index.html.twig', [
            'books' => $books,
            'overdueBooks' => $overdueBooks,
        ]);
    }

    #[Route('/overdue', name: 'admin_book_overdue', methods: ['GET'])]
    public function overdueBooks(EntityManagerInterface $entityManager): Response
    {
        $overdueBooks = $entityManager->getRepository(BookLoan::class)->findOverdueLoans();

        return $this->render('admin/book/overdue.html.twig', [
            'overdueBooks' => $overdueBooks,
        ]);
    }

    #[Route('/new', name: 'admin_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été ajouté avec succès.');
            return $this->redirectToRoute('admin_book_index');
        }

        return $this->render('admin/book/new.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été modifié avec succès.');
            return $this->redirectToRoute('admin_book_index');
        }

        return $this->render('admin/book/edit.html.twig', [
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

    #[Route('/{id}/delete', name: 'admin_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
            $this->addFlash('success', 'Le livre a été supprimé avec succès.');
        }

        return $this->redirectToRoute('admin_book_index');
    }

    #[Route('/{id}/return', name: 'admin_book_return', methods: ['POST'])]
    public function return(Book $book, EntityManagerInterface $entityManager): Response
    {
        $currentLoan = $book->getCurrentLoan();
        if ($currentLoan) {
            $currentLoan->setReturnDate(new \DateTime());
            $entityManager->flush();
            $this->addFlash('success', 'Le livre a été marqué comme retourné.');
        }

        return $this->redirectToRoute('admin_book_index');
    }

    #[Route('/comment/{id}/delete', name: 'admin_book_comment_delete', methods: ['POST'])]
    public function deleteComment(BookComment $comment, EntityManagerInterface $entityManager): Response
    {
        $book = $comment->getBook();
        $entityManager->remove($comment);
        $entityManager->flush();

        $this->addFlash('success', 'Le commentaire a été supprimé avec succès.');
        return $this->redirectToRoute('admin_book_show', ['id' => $book->getId()]);
    }

    #[Route('/loan/{id}/comment', name: 'admin_loan_comment', methods: ['POST'])]
    public function addLoanComment(Request $request, BookLoan $loan, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LoanCommentType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $loan->setAdminComment($form->get('comment')->getData());
            $entityManager->flush();
            $this->addFlash('success', 'Le commentaire a été ajouté à l\'emprunt.');
        }

        return $this->redirectToRoute('admin_book_show', ['id' => $loan->getBook()->getId()]);
    }
}
