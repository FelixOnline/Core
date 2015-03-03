<?php

use Phinx\Migration\AbstractMigration;

class ArticlePolls extends AbstractMigration
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
        $table = $this->table('polls');
        $table->addColumn('author', 'string', array('limit' => 16))
              ->addColumn('question', 'string')
              ->addColumn('ended', 'boolean', array('null' => false, 'default' => 0))
              ->addColumn('bottom', 'boolean', array('null' => false, 'default' => 0))
              ->addColumn('hide_results', 'boolean', array('null' => false, 'default' => 0))
              ->create();

        $table->addForeignKey('author', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('polls_option');
        $table->addColumn('poll', 'integer')
              ->addColumn('text', 'string')
              ->create();

        $table->addForeignKey('poll', 'polls', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('polls_response');
        $table->addColumn('poll', 'integer')
              ->addColumn('option', 'integer')
              ->addColumn('ip', 'string')
              ->addColumn('useragent', 'string')
              ->create();

        $table->addForeignKey('poll', 'polls', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('option', 'polls_option', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('article_polls');
        $table->addColumn('poll', 'integer')
              ->addColumn('article', 'integer')
              ->create();

        $table->addForeignKey('poll', 'polls', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('article', 'article', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

    }
}