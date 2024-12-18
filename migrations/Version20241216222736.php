<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241216222736 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, publication_year INT NOT NULL, summary LONGTEXT NOT NULL, image VARCHAR(255) DEFAULT NULL, `condition_state` VARCHAR(50) NOT NULL, is_available TINYINT(1) NOT NULL, isbn VARCHAR(13) DEFAULT NULL, stock INT NOT NULL, INDEX IDX_CBE5A33112469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_comment (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, user_id INT NOT NULL, content LONGTEXT NOT NULL, rating INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_7547AFA16A2B381 (book_id), INDEX IDX_7547AFAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_loan (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, book_id INT NOT NULL, loan_date DATETIME NOT NULL, due_date DATETIME NOT NULL, return_date DATETIME DEFAULT NULL, admin_comment LONGTEXT DEFAULT NULL, is_extended TINYINT(1) NOT NULL, INDEX IDX_DC4E460BA76ED395 (user_id), INDEX IDX_DC4E460B16A2B381 (book_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, capacity INT NOT NULL, has_wifi TINYINT(1) NOT NULL, has_projector TINYINT(1) NOT NULL, has_whiteboard TINYINT(1) NOT NULL, has_power_outlets TINYINT(1) NOT NULL, has_tv TINYINT(1) NOT NULL, has_air_conditioning TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE room_reservation (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, room_id INT NOT NULL, start_time DATETIME NOT NULL, end_time DATETIME NOT NULL, purpose LONGTEXT NOT NULL, INDEX IDX_56FDE76AA76ED395 (user_id), INDEX IDX_56FDE76A54177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type VARCHAR(20) NOT NULL, price NUMERIC(10, 2) NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME NOT NULL, UNIQUE INDEX UNIQ_A3C664D3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birth_date DATE NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', address VARCHAR(255) NOT NULL, postal_code VARCHAR(10) NOT NULL, city VARCHAR(255) NOT NULL, phone VARCHAR(20) NOT NULL, is_active TINYINT(1) NOT NULL, is_banned TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE book_comment ADD CONSTRAINT FK_7547AFA16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_comment ADD CONSTRAINT FK_7547AFAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE book_loan ADD CONSTRAINT FK_DC4E460BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE book_loan ADD CONSTRAINT FK_DC4E460B16A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE room_reservation ADD CONSTRAINT FK_56FDE76AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE room_reservation ADD CONSTRAINT FK_56FDE76A54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE loan_note ADD CONSTRAINT FK_1E1080605D53AE19 FOREIGN KEY (book_loan_id) REFERENCES book_loan (id)');
        $this->addSql('ALTER TABLE loan_note ADD CONSTRAINT FK_1E108060B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE loan_note DROP FOREIGN KEY FK_1E1080605D53AE19');
        $this->addSql('ALTER TABLE loan_note DROP FOREIGN KEY FK_1E108060B03A8386');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33112469DE2');
        $this->addSql('ALTER TABLE book_comment DROP FOREIGN KEY FK_7547AFA16A2B381');
        $this->addSql('ALTER TABLE book_comment DROP FOREIGN KEY FK_7547AFAA76ED395');
        $this->addSql('ALTER TABLE book_loan DROP FOREIGN KEY FK_DC4E460BA76ED395');
        $this->addSql('ALTER TABLE book_loan DROP FOREIGN KEY FK_DC4E460B16A2B381');
        $this->addSql('ALTER TABLE room_reservation DROP FOREIGN KEY FK_56FDE76AA76ED395');
        $this->addSql('ALTER TABLE room_reservation DROP FOREIGN KEY FK_56FDE76A54177093');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3A76ED395');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_comment');
        $this->addSql('DROP TABLE book_loan');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE room_reservation');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
