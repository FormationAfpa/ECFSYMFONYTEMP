<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BookFixtures extends Fixture implements DependentFixtureInterface
{
    private const BOOKS = [
        [
            'title' => 'Le Petit Prince',
            'author' => 'Antoine de Saint-Exupéry',
            'publicationYear' => 1943,
            'summary' => 'Un pilote d\'avion, qui a dû atterrir en urgence dans le désert du Sahara, rencontre un mystérieux petit prince venu d\'une autre planète. Au fil des jours, le petit prince raconte son voyage, sa rose unique qu\'il a laissée sur son astéroïde.',
            'condition' => Book::CONDITION_EXCELLENT,
            'image' => 'petit-prince.jpg',
            'category' => 'Jeunesse',
            'isbn' => '9782070408504',
            'reference' => 'book_le_petit_prince'
        ],
        [
            'title' => '1984',
            'author' => 'George Orwell',
            'publicationYear' => 1949,
            'summary' => 'Dans un monde totalitaire dominé par Big Brother, Winston Smith travaille au ministère de la Vérité où il réécrit l\'histoire selon la doctrine du Parti. Mais il commence à douter et à se rebeller intérieurement contre le système.',
            'condition' => Book::CONDITION_GOOD,
            'image' => '1984.jpg',
            'category' => 'Science-Fiction',
            'isbn' => '9780451524935',
            'reference' => 'book_1984'
        ],
        [
            'title' => 'Les Misérables',
            'author' => 'Victor Hugo',
            'publicationYear' => 1862,
            'summary' => 'L\'histoire suit le parcours de Jean Valjean, un ancien forçat qui tente de se racheter, tout en étant poursuivi par l\'inspecteur Javert. À travers ce personnage et ceux qu\'il rencontre, notamment Cosette, Hugo dépeint la société française du XIXe siècle.',
            'condition' => Book::CONDITION_FAIR,
            'image' => 'miserables.jpeg',
            'category' => 'Roman',
            'reference' => 'book_les_miserables'
        ],
        [
            'title' => 'L\'Odyssée',
            'author' => 'Homère',
            'publicationYear' => -800,
            'summary' => 'Après la guerre de Troie, Ulysse tente de rentrer chez lui à Ithaque. Son voyage de retour durera dix ans, pendant lesquels il affrontera de nombreux périls et créatures mythologiques, tandis que son épouse Pénélope l\'attend fidèlement.',
            'condition' => Book::CONDITION_GOOD,
            'image' => 'odyssee.jpeg',
            'category' => 'Histoire',
            'reference' => 'book_l_odyssee'
        ],
        [
            'title' => 'Don Quichotte',
            'author' => 'Miguel de Cervantes',
            'publicationYear' => 1605,
            'summary' => 'L\'histoire d\'un gentilhomme qui, après avoir lu trop de romans de chevalerie, décide de devenir chevalier errant. Accompagné de son fidèle écuyer Sancho Panza, il part à l\'aventure et confond la réalité avec ses lectures.',
            'condition' => Book::CONDITION_EXCELLENT,
            'image' => 'don-quichotte.jpg',
            'category' => 'Roman',
            'reference' => 'book_don_quichotte'
        ],
        [
            'title' => 'Guerre et Paix',
            'author' => 'Léon Tolstoï',
            'publicationYear' => 1869,
            'summary' => 'Cette fresque épique suit cinq familles de l\'aristocratie russe pendant les guerres napoléoniennes. À travers les destins croisés de ses personnages, notamment Pierre Bézoukhov, André Bolkonski et Natacha Rostova, Tolstoï dépeint la société russe du début du XIXe siècle.',
            'condition' => Book::CONDITION_GOOD,
            'image' => 'guerre-et-paix.jpg',
            'category' => 'Roman',
            'reference' => 'book_guerre_et_paix'
        ],
        [
            'title' => 'Fahrenheit 451',
            'author' => 'Ray Bradbury',
            'publicationYear' => 1953,
            'summary' => 'Dans un futur dystopique, les livres sont interdits et les pompiers ont pour mission de les brûler. Guy Montag, un pompier, commence à remettre en question son rôle dans la société.',
            'condition' => Book::CONDITION_GOOD,
            'image' => 'fahrenheit451.jpg',
            'category' => 'Science-Fiction',
            'isbn' => '9781451673319',
            'reference' => 'book_fahrenheit_451'
        ]
    ];

    public function load(ObjectManager $manager): void
    {
        // Créer le dossier uploads/books s'il n'existe pas
        $uploadDir = __DIR__ . '/../../public/uploads/books';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        foreach (self::BOOKS as $bookData) {
            $book = new Book();
            $book->setTitle($bookData['title']);
            $book->setAuthor($bookData['author']);
            $book->setPublicationYear($bookData['publicationYear']);
            $book->setSummary($bookData['summary']);
            $book->setCondition($bookData['condition']);
            $book->setIsAvailable(true);
            
            if (isset($bookData['isbn'])) {
                $book->setIsbn($bookData['isbn']);
            }

            // Récupérer la catégorie
            if ($this->hasReference('category_' . strtolower($bookData['category']), Category::class)) {
                $category = $this->getReference('category_' . strtolower($bookData['category']), Category::class);
                $book->setCategory($category);
            }

            // Copier l'image depuis le dossier des fixtures vers le dossier uploads
            $sourceImage = __DIR__ . '/images/' . $bookData['image'];
            if (file_exists($sourceImage)) {
                copy($sourceImage, $uploadDir . '/' . $bookData['image']);
                $book->setImage($bookData['image']);
            }

            $manager->persist($book);
            
            // Ajouter la référence pour le livre
            if (isset($bookData['reference'])) {
                $this->addReference($bookData['reference'], $book);
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CategoryFixtures::class,
        ];
    }
}
