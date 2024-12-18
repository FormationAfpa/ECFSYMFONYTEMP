<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Entity\LoanNote;
use App\Form\BookType;
use App\Form\LoanNoteType;
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use App\Repository\BookLoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/books')]
#[IsGranted('ROLE_ADMIN')]
class AdminBookController extends AbstractController
{
    #[Route('/', name: 'app_admin_books', methods: ['GET'])]
    public function index(Request $request, BookRepository $bookRepository): Response
    {
        $showOverdue = $request->query->getBoolean('overdue', false);
        $books = $showOverdue ? $bookRepository->findOverdueBooks() : $bookRepository->findAll();

        return $this->render('admin/books/index.html.twig', [
            'books' => $books,
            'showOverdue' => $showOverdue,
        ]);
    }

    #[Route('/new', name: 'app_admin_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('books_directory'),
                        $newFilename
                    );
                    $book->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                    return $this->redirectToRoute('app_admin_book_new');
                }
            }

            $entityManager->persist($book);
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été créé avec succès');
            return $this->redirectToRoute('app_admin_books');
        }

        return $this->render('admin/books/new.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_book_show', methods: ['GET'])]
    public function show(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        $forms = [];
        foreach ($book->getBookLoans() as $loan) {
            $loanNote = new LoanNote();
            $form = $this->createForm(LoanNoteType::class, $loanNote, [
                'action' => $this->generateUrl('app_admin_book_loan_comment', [
                    'id' => $book->getId(),
                    'loanId' => $loan->getId()
                ])
            ]);
            $forms[$loan->getId()] = $form->createView();
        }

        return $this->render('admin/books/show.html.twig', [
            'book' => $book,
            'forms' => $forms,
        ]);
    }

    #[Route('/{id}/loan/{loanId}/comment', name: 'app_admin_book_loan_comment', methods: ['POST'])]
    public function addLoanComment(Request $request, Book $book, int $loanId, EntityManagerInterface $entityManager, BookLoanRepository $loanRepository): Response
    {
        $loan = $loanRepository->find($loanId);
        if (!$loan) {
            throw $this->createNotFoundException('Emprunt non trouvé');
        }

        $loanNote = new LoanNote();
        $form = $this->createForm(LoanNoteType::class, $loanNote);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $loanNote->setBookLoan($loan);
            $loanNote->setCreatedBy($this->getUser());
            $entityManager->persist($loanNote);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été ajouté avec succès');
        }

        return $this->redirectToRoute('app_admin_book_show', ['id' => $book->getId()]);
    }

    #[Route('/{id}/edit', name: 'app_admin_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('books_directory'),
                        $newFilename
                    );
                    
                    // Supprimer l'ancienne image si elle existe
                    if ($book->getImage()) {
                        $oldImagePath = $this->getParameter('books_directory').'/'.$book->getImage();
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                    }
                    
                    $book->setImage($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de l\'image');
                    return $this->redirectToRoute('app_admin_book_edit', ['id' => $book->getId()]);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été modifié avec succès');
            return $this->redirectToRoute('app_admin_book_show', ['id' => $book->getId()]);
        }

        return $this->render('admin/books/edit.html.twig', [
            'book' => $book,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            // Supprimer l'image si elle existe
            if ($book->getImage()) {
                $imagePath = $this->getParameter('books_directory').'/'.$book->getImage();
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $entityManager->remove($book);
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été supprimé avec succès');
        }

        return $this->redirectToRoute('app_admin_books');
    }

    #[Route('/{id}/return', name: 'app_admin_book_return', methods: ['POST'])]
    public function returnBook(Book $book, EntityManagerInterface $entityManager): Response
    {
        $currentLoan = null;
        foreach ($book->getBookLoans() as $loan) {
            if ($loan->getReturnedAt() === null) {
                $currentLoan = $loan;
                break;
            }
        }

        if ($currentLoan) {
            $currentLoan->setReturnedAt(new \DateTime());
            $book->setIsAvailable(true);
            $entityManager->flush();

            $this->addFlash('success', 'Le livre a été marqué comme retourné');
        } else {
            $this->addFlash('error', 'Ce livre n\'est pas actuellement emprunté');
        }

        return $this->redirectToRoute('app_admin_books');
    }

    #[Route('/comment/{id}', name: 'app_admin_comment_delete', methods: ['POST'])]
    public function deleteComment(Request $request, int $id, CommentRepository $commentRepository, EntityManagerInterface $entityManager): Response
    {
        $comment = $commentRepository->find($id);
        if (!$comment) {
            throw $this->createNotFoundException('Commentaire non trouvé');
        }

        if ($this->isCsrfTokenValid('delete'.$comment->getId(), $request->request->get('_token'))) {
            $bookId = $comment->getBook()->getId();
            $entityManager->remove($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Le commentaire a été supprimé avec succès');
            return $this->redirectToRoute('app_admin_book_show', ['id' => $bookId]);
        }

        return $this->redirectToRoute('app_admin_books');
    }
}
