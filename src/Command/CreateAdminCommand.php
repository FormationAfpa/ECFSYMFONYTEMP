<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates a new admin user',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // Vérifier si l'admin existe déjà
        $existingAdmin = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@bibliotheque.fr']);
        if ($existingAdmin) {
            $io->warning('Un administrateur existe déjà avec cet email.');
            return Command::SUCCESS;
        }

        $user = new User();
        $user->setEmail('admin@bibliotheque.fr');
        $user->setFirstname('Admin');
        $user->setLastname('System');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setBirthDate(new \DateTime('1990-01-01'));
        $user->setAddress('1 Rue de la Bibliothèque');
        $user->setPostalCode('75001');
        $user->setCity('Paris');
        $user->setPhone('0123456789');

        // Mot de passe sécurisé qui respecte les critères :
        // - Au moins 12 caractères
        // - Au moins une majuscule
        // - Au moins une minuscule
        // - Au moins un chiffre
        // - Au moins un caractère spécial
        $securePassword = 'Admin@Biblio2024!';
        
        $password = $this->passwordHasher->hashPassword($user, $securePassword);
        $user->setPassword($password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $io->success([
            'Administrateur créé avec succès.',
            'Email : admin@bibliotheque.fr',
            'Mot de passe : ' . $securePassword,
            'Ce mot de passe respecte les critères de sécurité suivants :',
            '- Au moins 12 caractères',
            '- Au moins une majuscule',
            '- Au moins une minuscule',
            '- Au moins un chiffre',
            '- Au moins un caractère spécial'
        ]);

        return Command::SUCCESS;
    }
}
