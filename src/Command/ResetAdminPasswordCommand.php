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
    name: 'app:reset-admin-password',
    description: 'Reset the admin password',
)]
class ResetAdminPasswordCommand extends Command
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

        $admin = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@bibliotheque.fr']);
        
        if (!$admin) {
            $io->error('Aucun administrateur trouvé avec l\'email admin@bibliotheque.fr');
            return Command::FAILURE;
        }

        // Nouveau mot de passe sécurisé
        $securePassword = 'Admin@Biblio2024!';
        
        $password = $this->passwordHasher->hashPassword($admin, $securePassword);
        $admin->setPassword($password);

        $this->entityManager->flush();

        $io->success([
            'Le mot de passe de l\'administrateur a été réinitialisé avec succès.',
            'Email : admin@bibliotheque.fr',
            'Nouveau mot de passe : ' . $securePassword,
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
