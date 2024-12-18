<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:update-user-created-at',
    description: 'Met à jour la date de création pour les utilisateurs existants',
)]
class UpdateUserCreatedAtCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();
        $now = new \DateTimeImmutable();

        foreach ($users as $user) {
            if ($user->getCreatedAt() === null) {
                $user->setCreatedAt($now);
            }
        }

        $this->entityManager->flush();
        $output->writeln('Les dates de création des utilisateurs ont été mises à jour.');

        return Command::SUCCESS;
    }
}
