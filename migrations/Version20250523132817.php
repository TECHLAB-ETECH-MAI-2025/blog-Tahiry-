<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250523132817 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP like_count
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article_like DROP email
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article_like ALTER article_id SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX article_user_like_unique ON article_like (article_id, user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD like_count INT DEFAULT 0 NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX article_user_like_unique
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article_like ADD email VARCHAR(45) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article_like ALTER article_id DROP NOT NULL
        SQL);
    }
}
