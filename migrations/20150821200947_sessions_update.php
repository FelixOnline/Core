<?php

use Phinx\Migration\AbstractMigration;

class SessionsUpdate extends AbstractMigration
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
        $table = $this->table('login');
        $table->addColumn('session_name', 'string', array('limit' => 100));
        $table->addIndex(array('session_name'));
        $table->update();
    }
}
