<?php

use Phinx\Migration\AbstractMigration;

class PollLocation extends AbstractMigration
{
    public function up()
    {
        $this->execute('SET SESSION SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";');
        
        $table = $this->table('polls_location');
        $table->addColumn('description', 'string')
              ->create();

        $this->execute('INSERT INTO polls_location VALUES("0", "Bottom of article"), ("1", "Top of article"), ("2", "Bottom and top of article")');

        $table = $this->table('polls');
        $table->addForeignKey('location', 'polls_location', 'id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'))
              ->update();
    }

    public function down()
    {
        $table = $this->table('polls');
        $table->dropForeignKey('location')
              ->update();

        $this->dropTable('polls_location');
    }
}
