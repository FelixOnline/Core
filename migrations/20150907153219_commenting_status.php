<?php

use Phinx\Migration\AbstractMigration;

class CommentingStatus extends AbstractMigration
{
    public function up()
    {
        $this->execute('SET SESSION SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";');
        
        $table = $this->table('article_comment_status');
        $table->addColumn('description', 'string')
              ->create();

        $this->execute('INSERT INTO article_comment_status VALUES("0", "Disabled"), ("1", "Enabled for all users"), ("2", "Enabled for logged in users only")');

        $table = $this->table('article');
        $table->addForeignKey('comment_status', 'article_comment_status', 'id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'))
              ->update();
    }

    public function down()
    {
        $table = $this->table('article');
        $table->dropForeignKey('comment_status')
              ->update();

        $this->dropTable('article_comment_status');
    }
}
