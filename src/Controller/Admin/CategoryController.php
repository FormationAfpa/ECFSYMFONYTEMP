<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/category')]
#[IsGranted('ROLE_ADMIN')]
class CategoryController extends AbstractController
{
    private $entityManager;
    private $categoryRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository
    ) {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
    }

    #[Route('/', name: 'admin_category_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $this->categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            $this->addFlash('success', 'La catégorie a été créée avec succès.');
            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', 'La catégorie a été modifiée avec succès.');
            return $this->redirectToRoute('admin_category_index');
        }

        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($category);
            $this->entityManager->flush();

            $this->addFlash('success', 'La catégorie a été supprimée avec succès.');
        }

        return $this->redirectToRoute('admin_category_index');
    }

    #[Route('/init', name: 'admin_category_init', methods: ['GET'])]
    public function initializeCategories(): Response
    {
        $categories = [
            [
                'name' => 'Roman',
                'description' => 'Romans de tous genres : contemporain, historique, d\'aventure, etc.'
            ],
            [
                'name' => 'Science-Fiction',
                'description' => 'Livres de science-fiction, dystopie, space opera, etc.'
            ],
            [
                'name' => 'Fantaisie',
                'description' => 'Livres de fantasy, heroic fantasy, urban fantasy, etc.'
            ],
            [
                'name' => 'Policier',
                'description' => 'Romans policiers, thrillers, enquêtes criminelles, etc.'
            ],
            [
                'name' => 'Histoire',
                'description' => 'Livres d\'histoire, biographies historiques, etc.'
            ],
            [
                'name' => 'Sciences',
                'description' => 'Livres scientifiques, vulgarisation scientifique, etc.'
            ],
            [
                'name' => 'Jeunesse',
                'description' => 'Livres pour enfants et adolescents'
            ],
            [
                'name' => 'Bande Dessinée',
                'description' => 'BD, comics, mangas, romans graphiques'
            ]
        ];

        foreach ($categories as $categoryData) {
            $category = new Category();
            $category->setName($categoryData['name']);
            $category->setDescription($categoryData['description']);
            $this->entityManager->persist($category);
        }

        $this->entityManager->flush();

        $this->addFlash('success', 'Les catégories ont été initialisées avec succès.');
        return $this->redirectToRoute('admin_category_index');
    }
}
