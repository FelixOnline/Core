<?php

use Phinx\Migration\AbstractMigration;

class MoreForeignKeys extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    public function change()
    {
        $table = $this->table('roles');
        $table->addForeignKey('parent', 'roles', 'id', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('user_roles');
        $table->addForeignKey('role', 'roles', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('user', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('user');
        $table->addForeignKey('image', 'image', 'id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'))
              ->save();
    }
}
