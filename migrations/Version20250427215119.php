<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250427215119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
        CREATE TABLE stade (
            id INT AUTO_INCREMENT NOT NULL,
            nom VARCHAR(255) NOT NULL,
            ville VARCHAR(255) NOT NULL,
            capacite_virage INT DEFAULT NULL,
            capacite_pelouse INT DEFAULT NULL,
            capacite_enceinte INT DEFAULT NULL,
            localisation TEXT DEFAULT NULL,
            image_file VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY(id)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
    SQL);
    }


    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE stade');
    }

}
