<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241218021500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de la table comment';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE comment (
            id INT AUTO_INCREMENT NOT NULL,
            book_id INT NOT NULL,
            user_id INT NOT NULL,
            rating INT NOT NULL,
            content LONGTEXT NOT NULL,
            created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\',
            INDEX IDX_9474526C16A2B381 (book_id),
            INDEX IDX_9474526CA76ED395 (user_id),
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C16A2B381');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('DROP TABLE comment');
    }
}
