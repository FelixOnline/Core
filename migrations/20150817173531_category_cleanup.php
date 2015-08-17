<?php

use Phinx\Migration\AbstractMigration;

class CategoryCleanup extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    public function up()
    {
        $table = $this->table('category');
        $table->addColumn('parent', 'integer', array('null' => true))
              ->dropForeignKey('top_slider_1')
              ->dropForeignKey('top_slider_2')
              ->dropForeignKey('top_slider_3')
              ->dropForeignKey('top_slider_4')
              ->dropForeignKey('top_sidebar_1')
              ->dropForeignKey('top_sidebar_2')
              ->dropForeignKey('top_sidebar_3')
              ->dropForeignKey('top_sidebar_4')
              ->dropForeignKey('top_sidebar_5')
              ->removeColumn('uri')
              ->removeColumn('colourclass')
              ->removeColumn('top_slider_1')
              ->removeColumn('top_slider_2')
              ->removeColumn('top_slider_3')
              ->removeColumn('top_slider_4')
              ->removeColumn('top_sidebar_1')
              ->removeColumn('top_sidebar_2')
              ->removeColumn('top_sidebar_3')
              ->removeColumn('top_sidebar_4')
              ->removeColumn('top_sidebar_5')
              ->addForeignKey('parent', 'category', 'id', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->save();
    }

    public function down()
    {
        $table = $this->table('category');
        $table->dropForeignKey('parent')
              ->removeColumn('parent')
              ->addColumn('uri', 'string')
              ->addColumn('colourclass', 'string')
              ->addColumn('top_slider_1', 'integer', array('null' => true))
              ->addColumn('top_slider_2', 'integer', array('null' => true))
              ->addColumn('top_slider_3', 'integer', array('null' => true))
              ->addColumn('top_slider_4', 'integer', array('null' => true))
              ->addColumn('top_sidebar_1', 'integer', array('null' => true))
              ->addColumn('top_sidebar_2', 'integer', array('null' => true))
              ->addColumn('top_sidebar_3', 'integer', array('null' => true))
              ->addColumn('top_sidebar_4', 'integer', array('null' => true))
              ->addColumn('top_sidebar_5', 'integer', array('null' => true))
              ->addForeignKey('top_slider_1', 'article', 'id', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->addForeignKey('top_slider_2', 'article', 'id', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->addForeignKey('top_slider_3', 'article', 'id', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->addForeignKey('top_slider_4', 'article', 'id', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->addForeignKey('top_sidebar_1', 'article', 'id', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->addForeignKey('top_sidebar_2', 'article', 'id', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->addForeignKey('top_sidebar_3', 'article', 'id', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->addForeignKey('top_sidebar_4', 'article', 'id', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->addForeignKey('top_sidebar_5', 'article', 'id', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->save();
    }
}
