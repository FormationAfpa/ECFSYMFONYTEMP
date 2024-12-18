<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\BookComment;
use App\Entity\BookLoan;
use App\Form\BookCommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('', name: 'app_book_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $books = $entityManager->getRepository(Book::class)->findAll();

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/{id}', name: 'app_book_show', methods: ['GET', 'POST'])]
    public function show(Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new BookComment();
        $comment->setBook($book);
        $comment->setCreatedAt(new \DateTime());
        
        if ($this->getUser()) {
            $comment->setUser($this->getUser());
        }
        
        $form = $this->createForm(BookCommentType::class, $comment);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($comment);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre commentaire a été publié avec succès !');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        return $this->render('book/show.html.twig', [
            'book' => $book,
            'commentForm' => $form->createView(),
        ]);
    }

    #[Route('/{id}/borrow', name: 'app_book_borrow', methods: ['POST'])]
    public function borrow(Book $book, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        if (!$user->getSubscription()) {
            $this->addFlash('error', 'Vous devez avoir un abonnement actif pour emprunter des livres.');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        if (!$book->isAvailable()) {
            $this->addFlash('error', 'Ce livre n\'est pas disponible pour l\'emprunt.');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        $loan = new BookLoan();
        $loan->setBook($book);
        $loan->setUser($user);
        
        $entityManager->persist($loan);
        $entityManager->flush();

        $this->addFlash('success', 'Le livre a été emprunté avec succès. Retour prévu le ' . $loan->getDueDate()->format('d/m/Y'));
        return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
    }

    #[Route('/{id}/return', name: 'app_book_return', methods: ['POST'])]
    public function return(Book $book, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $currentLoan = $book->getCurrentLoan();
        if (!$currentLoan || $currentLoan->getUser() !== $user) {
            $this->addFlash('error', 'Vous ne pouvez pas retourner ce livre.');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        $currentLoan->setReturnDate(new \DateTime());
        $entityManager->flush();

        $this->addFlash('success', 'Le livre a été retourné avec succès.');
        return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
    }

    #[Route('/{id}/extend', name: 'app_book_extend', methods: ['POST'])]
    public function extend(Book $book, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $currentLoan = $book->getCurrentLoan();
        if (!$currentLoan || $currentLoan->getUser() !== $user) {
            $this->addFlash('error', 'Vous ne pouvez pas prolonger cet emprunt.');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        if ($currentLoan->isExtended()) {
            $this->addFlash('error', 'Cet emprunt a déjà été prolongé.');
            return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
        }

        $currentLoan->setDueDate((new \DateTime())->modify('+6 days'));
        $currentLoan->setIsExtended(true);
        $entityManager->flush();

        $this->addFlash('success', 'L\'emprunt a été prolongé avec succès. Nouvelle date de retour : ' . $currentLoan->getDueDate()->format('d/m/Y'));
        return $this->redirectToRoute('app_book_show', ['id' => $book->getId()]);
    }
}
