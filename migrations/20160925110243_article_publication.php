<?php

use Phinx\Migration\AbstractMigration;

class ArticlePublication extends AbstractMigration
{
    public function up()
    {
        // Create table
        $table = $this->table('article_publication');

        $table->addColumn('article', 'integer')
              ->addColumn('publication_date', 'datetime')
              ->addColumn('published_by', 'string', array('limit' => 16))
              ->addColumn('republished', 'boolean', array('default' => 0))
              ->addColumn('deleted', 'boolean', array('default' => 0))
              ->addForeignKey('article', 'article', 'id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'))
              ->addForeignKey('published_by', 'user', 'user', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'))
              ->create();

        // Now migrate over publication status
        foreach($this->fetchAll('SELECT * FROM article WHERE published > 0') as $article) {
            $this->execute('INSERT INTO article_publication VALUES(NULL, '.$article['id'].', "'.$article['published'].'", "'.$article['approvedby'].'", 0, 0);'); 
        }

        // Finally, delete the old columns
        $table = $this->table('article');
        $table->dropForeignKey('approvedby')
              ->removeColumn('published')
              ->removeColumn('approvedby')
              ->save();
    }

    public function down()
    {
        // Create columns in article table
        $table = $this->table('article');

        $table->addColumn('published', 'datetime')
              ->addColumn('approvedby', 'string', array('limit' => 16))
              ->addForeignKey('approvedby', 'user', 'id', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'))
              ->save();

        // Now migrate over publication status
        foreach($this->fetchAll('SELECT * FROM article_publication WHERE republished = 0') as $article) {
            $this->execute('UPDATE article SET published = "'.$article['publication_date'].'" AND approvedby = "'.$article['published_by'].'" WHERE id = '.$article['article']); 
        }

        // Finally, delete the old table
        $this->dropTable('article_publication');
    }
}
