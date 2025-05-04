<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250503213501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Ajout de l'abréviation à l'équipe
        $this->addSql(<<<'SQL'
        ALTER TABLE equipe ADD abreviation VARCHAR(10) NOT NULL
    SQL);

        // Étape 1 : Ajouter la colonne mais permettre temporairement NULL pour éviter l’erreur
        $this->addSql(<<<'SQL'
        ALTER TABLE user ADD date_inscription DATETIME DEFAULT NULL
    SQL);

        // Étape 2 : Mettre une date valide pour les utilisateurs existants
        $this->addSql(<<<'SQL'
        UPDATE user SET date_inscription = NOW() WHERE date_inscription IS NULL
    SQL);

        // Étape 3 : Rendre le champ NOT NULL après mise à jour
        $this->addSql(<<<'SQL'
        ALTER TABLE user MODIFY date_inscription DATETIME NOT NULL
    SQL);
    }


    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe DROP abreviation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP date_inscription
        SQL);
    }
}
