<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PortalsMigration extends AbstractMigration
{
    public function change(): void
    {
        $portals = $this->table('portals');
        $portals->addColumn('name', 'string', array('limit' => 255))
            ->addColumn('created_at', 'timestamp', array('null' => true))
            ->addColumn('updated_at', 'timestamp', array('null' => true))
            ->create();
    }
}
