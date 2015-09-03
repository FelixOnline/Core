<?php

use Phinx\Migration\AbstractMigration;

class RemoveImageOffset extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('image');

        $table->removeColumn('v_offset')
              ->removeColumn('h_offset')
              ->save();
    }

    public function down()
    {
        $table = $this->table('image');

        $table->addColumn('v_offset', 'integer', array('default' => 0))
              ->addColumn('h_offset', 'integer', array('default' => 0))
              ->save();
    }
}
