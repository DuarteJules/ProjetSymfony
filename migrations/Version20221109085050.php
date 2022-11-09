<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221109085050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidate_skill (candidate_id INT NOT NULL, skill_id INT NOT NULL, INDEX IDX_66DD0F8B91BD8781 (candidate_id), INDEX IDX_66DD0F8B5585C142 (skill_id), PRIMARY KEY(candidate_id, skill_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidate_skill ADD CONSTRAINT FK_66DD0F8B91BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_skill ADD CONSTRAINT FK_66DD0F8B5585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skill_candidate DROP FOREIGN KEY FK_9B43B5195585C142');
        $this->addSql('ALTER TABLE skill_candidate DROP FOREIGN KEY FK_9B43B51991BD8781');
        $this->addSql('DROP TABLE skill_candidate');
        $this->addSql('ALTER TABLE candidate CHANGE email email VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE skill_candidate (skill_id INT NOT NULL, candidate_id INT NOT NULL, INDEX IDX_9B43B51991BD8781 (candidate_id), INDEX IDX_9B43B5195585C142 (skill_id), PRIMARY KEY(skill_id, candidate_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE skill_candidate ADD CONSTRAINT FK_9B43B5195585C142 FOREIGN KEY (skill_id) REFERENCES skill (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE skill_candidate ADD CONSTRAINT FK_9B43B51991BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_skill DROP FOREIGN KEY FK_66DD0F8B91BD8781');
        $this->addSql('ALTER TABLE candidate_skill DROP FOREIGN KEY FK_66DD0F8B5585C142');
        $this->addSql('DROP TABLE candidate_skill');
        $this->addSql('ALTER TABLE candidate CHANGE email email VARCHAR(100) NOT NULL');
    }
}
