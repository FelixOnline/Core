<?php

use Phinx\Migration\AbstractMigration;

class LinksTable extends AbstractMigration
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
        $table = $this->table('link', array('id' => false, 'primary_key' => array('link')));
        $table->addColumn('link', 'string', array('limit' => 100))
              ->addColumn('url', 'string', array('limit' => 500))
              ->addColumn('active', 'boolean')
              ->create();
    }
}
