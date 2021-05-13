<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class PixelsMigration extends AbstractMigration
{
    public function change(): void
    {
        $pixels = $this->table('pixels');
        $pixels->addColumn('pixelType', 'string', array('limit' => 255))
            ->addColumn('userId', 'integer', array('limit' => 11))
            ->addColumn('occuredOn', 'date', array('null' => true))
            ->addColumn('portalId', 'integer', array('limit' => 11))
            ->addColumn('created_at', 'timestamp', array('null' => true))
            ->addColumn('updated_at', 'timestamp', array('null' => true))
            ->addForeignKey('userId',
                              'users',
                              'id',
                              ['delete'=> 'RESTRICT', 'update'=> 'RESTRICT', 'constraint' => 'pixels_user_id'])
            ->addForeignKey('portalId',
                              'portals',
                              'id',
                              ['delete'=> 'RESTRICT', 'update'=> 'RESTRICT', 'constraint' => 'pixels_portal_id'])
            ->create();
    }
}
