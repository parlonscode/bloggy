<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220928211239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove unique_slug_for_publish_date and use only slug';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX unique_slug_for_publish_date');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_885DBAFA989D9B62 ON posts (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_885DBAFA989D9B62');
        $this->addSql('CREATE UNIQUE INDEX unique_slug_for_publish_date ON "posts" (published_at, slug)');
    }
}
