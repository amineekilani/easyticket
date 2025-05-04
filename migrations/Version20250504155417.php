<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250504155417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE match_football (id INT AUTO_INCREMENT NOT NULL, equipe1_id INT DEFAULT NULL, equipe2_id INT DEFAULT NULL, stade_id INT DEFAULT NULL, date_et_heure DATETIME NOT NULL, nbr_billets_virage INT DEFAULT NULL, prix_billet_virage DOUBLE PRECISION DEFAULT NULL, nbr_billets_pelouse INT DEFAULT NULL, prix_billet_pelouse DOUBLE PRECISION DEFAULT NULL, nbr_billets_enceinte INT DEFAULT NULL, prix_billet_enceinte DOUBLE PRECISION DEFAULT NULL, INDEX IDX_73A4FC084265900C (equipe1_id), INDEX IDX_73A4FC0850D03FE2 (equipe2_id), INDEX IDX_73A4FC086538AB43 (stade_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE match_football ADD CONSTRAINT FK_73A4FC084265900C FOREIGN KEY (equipe1_id) REFERENCES equipe (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE match_football ADD CONSTRAINT FK_73A4FC0850D03FE2 FOREIGN KEY (equipe2_id) REFERENCES equipe (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE match_football ADD CONSTRAINT FK_73A4FC086538AB43 FOREIGN KEY (stade_id) REFERENCES stade (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE match_football DROP FOREIGN KEY FK_73A4FC084265900C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE match_football DROP FOREIGN KEY FK_73A4FC0850D03FE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE match_football DROP FOREIGN KEY FK_73A4FC086538AB43
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE match_football
        SQL);
    }
}
