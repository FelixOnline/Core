<?php

use Phinx\Migration\AbstractMigration;

class AdvertsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('advert');
        $table->addColumn('details', 'string', array('limit' => 200))
              ->addColumn('image', 'integer')
              ->addColumn('url', 'string', array('limit' => 500))
              ->addColumn('start_date', 'datetime')
              ->addColumn('end_date', 'datetime', array('null' => true))
              ->addColumn('max_impressions', 'integer')
              ->addColumn('views', 'integer', array('default' => 0))
              ->addColumn('clicks', 'integer', array('default' => 0))
              ->addColumn('frontpage', 'boolean', array('default' => 0))
              ->addColumn('categories', 'boolean', array('default' => 0))
              ->addColumn('articles', 'boolean', array('default' => 0))
              ->addIndex(array('start_date'))
              ->addIndex(array('end_date'))
              ->addIndex(array('frontpage'))
              ->addIndex(array('categories'))
              ->addIndex(array('articles'))
              ->addForeignKey('image', 'image', 'id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'))
              ->create();

        $table = $this->table('advert_category');
        $table->addColumn('advert', 'integer')
              ->addColumn('category', 'integer')
              ->addIndex(array('advert'))
              ->addIndex(array('category'))
              ->addForeignKey('advert', 'advert', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('category', 'category', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->create();
    }
}
