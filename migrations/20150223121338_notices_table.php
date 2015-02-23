<?php

use Phinx\Migration\AbstractMigration;

class NoticesTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     */
    public function change()
    {
        $table = $this->table('notices');
        $table->addColumn('author', 'string', array('limit' => 16))
              ->addColumn('text', 'integer')
              ->addColumn('start_time', 'datetime')
              ->addColumn('end_time', 'datetime')
              ->addColumn('hidden', 'boolean', array('null' => false, 'default' => 0))
              ->addColumn('frontpage', 'boolean', array('null' => false, 'default' => 1))
              ->addColumn('sort_order', 'integer')
              ->create();

        $table->addForeignKey('text', 'text_story', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('author', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

    }
}