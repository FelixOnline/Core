<?php

use Phinx\Migration\AbstractMigration;

class ArticleTopic extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('topic', array('id' => false, 'primary_key' => array('slug')));

        $table->addColumn('slug', 'string', array('length' => 100))
              ->addColumn('name', 'string', array('length' => 255))
              ->addColumn('text', 'text')
              ->addColumn('image', 'integer')
              ->addColumn('disabled', 'boolean')
              ->addIndex(array('image'))
              ->addForeignKey('image', 'image', 'id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'))
              ->create();

        $table = $this->table('article_topic');

        $table->addColumn('article', 'integer')
              ->addColumn('topic', 'string', array('length' => 100))
              ->addIndex(array('article', 'topic'))
              ->addForeignKey('topic', 'topic', 'slug', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('article', 'article', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->create();
    }
}
