<?php

use Phinx\Migration\AbstractMigration;

class TextConversion extends AbstractMigration
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
        $table = $this->table('text_story');
        $table->addColumn('converted', 'boolean', array('null' => false, 'default' => 0));
        $table->save();
    }
}
