<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230817114502 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_32EB52E8E7927C74 ON administrateur');
        $this->addSql('ALTER TABLE administrateur DROP email, DROP roles, DROP password, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE administrateur ADD CONSTRAINT FK_32EB52E8BF396750 FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX UNIQ_C08D8005E7927C74 ON theatre');
        $this->addSql('ALTER TABLE theatre DROP email, DROP roles, DROP password, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE theatre ADD CONSTRAINT FK_C08D8005BF396750 FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur ADD dtype VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE administrateur DROP FOREIGN KEY FK_32EB52E8BF396750');
        $this->addSql('ALTER TABLE administrateur ADD email VARCHAR(180) NOT NULL, ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD password VARCHAR(255) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32EB52E8E7927C74 ON administrateur (email)');
        $this->addSql('ALTER TABLE theatre DROP FOREIGN KEY FK_C08D8005BF396750');
        $this->addSql('ALTER TABLE theatre ADD email VARCHAR(180) NOT NULL, ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', ADD password VARCHAR(255) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C08D8005E7927C74 ON theatre (email)');
        $this->addSql('ALTER TABLE utilisateur DROP dtype');
    }
}
