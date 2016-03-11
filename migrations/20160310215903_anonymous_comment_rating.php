<?php

use Phinx\Migration\AbstractMigration;

class AnonymousCommentRating extends AbstractMigration
{
    public function up()
    {
        $this->execute('DROP INDEX comment_like_check ON comment_like');

        $table = $this->table('comment_like');
        $table->dropForeignKey('user')
              ->addColumn('ip', 'string', array('length' => 255, 'null' => true))
              ->addColumn('user_agent', 'string', array('length' => 255, 'null' => true))
              ->removeColumn('user')
              ->save();
    }

    public function down()
    {
        $table = $this->table('comment_like');
        $table->addColumn('user', 'string', array('length' => 16))
              ->removeColumn('user_agent')
              ->removeColumn('ip')
              ->addForeignKey('user', 'user', 'user', array('delete'=> 'SET_NULL', 'update'=> 'NO_ACTION'))
              ->save();
    }
}
