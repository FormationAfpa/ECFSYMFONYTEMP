<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        // Créer un administrateur
        $admin = new User();
        $admin->setEmail('admin@bibliotheque.fr');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Admin@123!'));
        $admin->setFirstName('Admin');
        $admin->setLastName('Système');
        $admin->setBirthDate(new \DateTime('1990-01-01'));
        $admin->setAddress('1 rue de la Bibliothèque');
        $admin->setPostalCode('75001');
        $admin->setCity('Paris');
        $admin->setPhone('0123456789');
        $admin->setIsActive(true);
        $admin->setIsBanned(false);
        $manager->persist($admin);
        $this->addReference('admin_user', $admin);

        // Créer un utilisateur normal
        $user1 = new User();
        $user1->setEmail('user@example.com');
        $user1->setRoles(['ROLE_USER']);
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'User@123!'));
        $user1->setFirstName('Jean');
        $user1->setLastName('Dupont');
        $user1->setBirthDate(new \DateTime('1995-05-15'));
        $user1->setAddress('15 rue des Lecteurs');
        $user1->setPostalCode('75002');
        $user1->setCity('Paris');
        $user1->setPhone('0987654321');
        $user1->setIsActive(true);
        $user1->setIsBanned(false);
        $manager->persist($user1);
        $this->addReference('user_1', $user1);

        // Créer un deuxième utilisateur
        $user2 = new User();
        $user2->setEmail('marie@example.com');
        $user2->setRoles(['ROLE_USER']);
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'User@123!'));
        $user2->setFirstName('Marie');
        $user2->setLastName('Martin');
        $user2->setBirthDate(new \DateTime('1992-08-20'));
        $user2->setAddress('25 rue des Livres');
        $user2->setPostalCode('75003');
        $user2->setCity('Paris');
        $user2->setPhone('0765432198');
        $user2->setIsActive(true);
        $user2->setIsBanned(false);
        $manager->persist($user2);
        $this->addReference('user_2', $user2);

        $manager->flush();
    }
}
