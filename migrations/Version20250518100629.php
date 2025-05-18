<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250518100629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE billet (id INT AUTO_INCREMENT NOT NULL, commande_id INT NOT NULL, match_id INT NOT NULL, section VARCHAR(255) NOT NULL, seat_number VARCHAR(255) NOT NULL, price DOUBLE PRECISION NOT NULL, qr_code VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_1F034AF682EA2E54 (commande_id), INDEX IDX_1F034AF62ABEACD6 (match_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', total DOUBLE PRECISION NOT NULL, commande_details LONGTEXT DEFAULT NULL, stripe_session_id VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_6EEAA67DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE billet ADD CONSTRAINT FK_1F034AF682EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE billet ADD CONSTRAINT FK_1F034AF62ABEACD6 FOREIGN KEY (match_id) REFERENCES match_football (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE match_football DROP billets_virage_vendus, DROP billets_pelouse_vendus, DROP billets_enceinte_vendus
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE billet DROP FOREIGN KEY FK_1F034AF682EA2E54
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE billet DROP FOREIGN KEY FK_1F034AF62ABEACD6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67DA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE billet
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE commande
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE match_football ADD billets_virage_vendus INT DEFAULT NULL, ADD billets_pelouse_vendus INT DEFAULT NULL, ADD billets_enceinte_vendus INT DEFAULT NULL
        SQL);
    }
}
