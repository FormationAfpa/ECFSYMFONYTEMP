<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
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
            $manager->persist($category);
            
            // Ajouter la référence pour la catégorie
            $this->addReference('category_' . strtolower($categoryData['name']), $category);
        }

        $manager->flush();
    }
}
