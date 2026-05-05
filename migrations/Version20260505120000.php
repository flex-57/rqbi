<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260505120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Supprime les blocs de type contact (remplacés par map + form + cards)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("DELETE FROM block WHERE type = 'contact'");
    }

    public function down(Schema $schema): void
    {
        // Irréversible : les données contact ne peuvent pas être reconstituées
    }
}
