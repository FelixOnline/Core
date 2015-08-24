<?php

use Phinx\Migration\AbstractMigration;

class CommentCleanup extends AbstractMigration
{
    public function up()
    {
        $this->dropTable('comment_info');
        $this->dropTable('comment_spam');
    }

    public function down()
    {
        $table = $this->table('comment_info');
        $table->addColumn('type', 'string', array('length' => 30, 'null' => true))
              ->addColumn('count', 'integer', array('null' => true))
              ->create();

        $table = $this->table('comment_spam', array('id' => false));
        $table->addColumn('IP', 'string', array('length' => 15, 'null' => true))
              ->addColumn('date', 'datetime', array('null' => true))
              ->create();
    }
}
