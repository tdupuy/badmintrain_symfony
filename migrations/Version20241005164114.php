<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241005164114 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE matches_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE players_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE teams_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE matches (id INT NOT NULL, idteam1 INT NOT NULL, idteam2 INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE players (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE teams (id INT NOT NULL, player1 INT NOT NULL, player2 INT DEFAULT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE matches_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE players_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE teams_id_seq CASCADE');
        $this->addSql('DROP TABLE matches');
        $this->addSql('DROP TABLE players');
        $this->addSql('DROP TABLE teams');
    }
}
