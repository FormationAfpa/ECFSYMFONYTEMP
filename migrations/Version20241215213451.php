<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241215213451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE loan_note (id INT AUTO_INCREMENT NOT NULL, book_loan_id INT NOT NULL, created_by_id INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1E1080605D53AE19 (book_loan_id), INDEX IDX_1E108060B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE loan_note ADD CONSTRAINT FK_1E1080605D53AE19 FOREIGN KEY (book_loan_id) REFERENCES book_loan (id)');
        $this->addSql('ALTER TABLE loan_note ADD CONSTRAINT FK_1E108060B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loan_note DROP FOREIGN KEY FK_1E1080605D53AE19');
        $this->addSql('ALTER TABLE loan_note DROP FOREIGN KEY FK_1E108060B03A8386');
        $this->addSql('DROP TABLE loan_note');
    }
}
