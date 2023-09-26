<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230819161513 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ouvreur CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE ouvreur ADD CONSTRAINT FK_AE869610BF396750 FOREIGN KEY (id) REFERENCES utilisateur (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ouvreur DROP FOREIGN KEY FK_AE869610BF396750');
        $this->addSql('ALTER TABLE ouvreur CHANGE id id INT AUTO_INCREMENT NOT NULL');
    }
}
