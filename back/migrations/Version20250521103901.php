<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250521103901 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE article_category DROP CONSTRAINT fk_53a4edaa7294869c
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article_category DROP CONSTRAINT fk_53a4edaa12469de2
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE article_category
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP CONSTRAINT fk_23a0e66a76ed395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_23a0e66a76ed395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD article_user_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP user_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ALTER title TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ALTER content TYPE VARCHAR(255)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ALTER updated_at SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN article.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN article.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E664ABC9722 FOREIGN KEY (article_user_id) REFERENCES "users" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_23A0E664ABC9722 ON article (article_user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE article_category (article_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(article_id, category_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_53a4edaa12469de2 ON article_category (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_53a4edaa7294869c ON article_category (article_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article_category ADD CONSTRAINT fk_53a4edaa7294869c FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article_category ADD CONSTRAINT fk_53a4edaa12469de2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP CONSTRAINT FK_23A0E664ABC9722
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_23A0E664ABC9722
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD user_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP article_user_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ALTER title TYPE VARCHAR(100)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ALTER content TYPE TEXT
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ALTER updated_at DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN article.created_at IS NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN article.updated_at IS NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT fk_23a0e66a76ed395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_23a0e66a76ed395 ON article (user_id)
        SQL);
    }
}
