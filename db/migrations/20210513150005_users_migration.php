<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UsersMigration extends AbstractMigration
{
    public function change(): void
    {
        $users = $this->table('users');
        $users->addColumn('name', 'string', array('limit' => 255))
            ->addColumn('password', 'string', array('limit' => 255))
            ->addColumn('email', 'string', array('limit' => 255))
            ->addColumn('activ', 'integer', array('limit' => 1, 'default' => 0))
            ->addColumn('activ_code', 'string', array('limit' => 32))
            ->addColumn('created_at', 'timestamp', array('null' => true))
            ->addColumn('updated_at', 'timestamp', array('null' => true))
            ->create();
    }
}
