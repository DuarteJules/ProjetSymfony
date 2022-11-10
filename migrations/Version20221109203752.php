<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221109203752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job_skill (job_id INT NOT NULL, skill_id INT NOT NULL, INDEX IDX_5F615907BE04EA9 (job_id), INDEX IDX_5F6159075585C142 (skill_id), PRIMARY KEY(job_id, skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job_skill ADD CONSTRAINT FK_5F615907BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_skill ADD CONSTRAINT FK_5F6159075585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skill_job DROP FOREIGN KEY FK_88B2D16BE04EA9');
        $this->addSql('ALTER TABLE skill_job DROP FOREIGN KEY FK_88B2D165585C142');
        $this->addSql('DROP TABLE skill_job');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE skill_job (skill_id INT NOT NULL, job_id INT NOT NULL, INDEX IDX_88B2D16BE04EA9 (job_id), INDEX IDX_88B2D165585C142 (skill_id), PRIMARY KEY(skill_id, job_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE skill_job ADD CONSTRAINT FK_88B2D16BE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skill_job ADD CONSTRAINT FK_88B2D165585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job_skill DROP FOREIGN KEY FK_5F615907BE04EA9');
        $this->addSql('ALTER TABLE job_skill DROP FOREIGN KEY FK_5F6159075585C142');
        $this->addSql('DROP TABLE job_skill');
    }
}
