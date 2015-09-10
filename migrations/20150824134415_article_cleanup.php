<?php

use Phinx\Migration\AbstractMigration;

class ArticleCleanup extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('article');
        $table->addColumn('img_caption', 'string', array('after' => 'img1', 'limit' => 300, 'null' => true))
              ->dropForeignKey('img2')
              ->dropForeignKey('text2')
              ->dropForeignKey('author')
              ->removeColumn('short_title')
              ->removeColumn('text2')
              ->removeColumn('img2')
              ->removeColumn('img2lr')
              ->removeColumn('hits')
              ->removeColumn('author')
              ->removeColumn('short_desc')
              ->addForeignKey('img1', 'image', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->update();

        foreach($this->fetchAll('SELECT * FROM image WHERE caption IS NOT NULL') as $image) {
            $this->execute('UPDATE article SET img_caption = "'.str_replace('"', '\"', $image['caption']).'" WHERE img1 = '.$image['id']);
        }

        $table = $this->table('image');
        $table->removeColumn('caption')
              ->save();

        $this->dropTable('article_topic');
        $this->dropTable('topic');

        $this->execute('ALTER TABLE `article_author` ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);');
    }

    public function down()
    {
        $table = $this->table('image');
        $table->addColumn('caption', 'string', array('limit' => 300))
              ->save();

        foreach($this->fetchAll('SELECT * FROM article WHERE img1 IS NOT NULL') as $article) {
            $this->execute('UPDATE image SET caption = "'.str_replace('"', '\"', $article['img_caption']).'" WHERE id = '.$article['img1']);
        }

        $table = $this->table('article');
        $table->removeColumn('img_caption')
              ->addColumn('short_title', 'string', array('limit' => 20))
              ->addColumn('text2', 'integer', array('null' => true))
              ->addColumn('img2', 'integer', array('null' => true))
              ->addColumn('img2lr', 'boolean')
              ->addColumn('hits', 'integer')
              ->addColumn('short_desc', 'string', array('limit' => 100))
              ->dropForeignKey('img1')
              ->addForeignKey('text2', 'text_story', 'id', array('delete'=> 'RESTRICT', 'update'=> 'RESTRICT'))
              ->addForeignKey('img2', 'image', 'id', array('delete'=> 'RESTRICT', 'update'=> 'RESTRICT'))
              ->save();

        foreach($this->fetchAll('SELECT * FROM article') as $article) {
            $hits = $this->fetchRow('SELECT COUNT("article") AS hits FROM article_visit WHERE repeat_visit = 0 AND article = '.$article['id']);
            $this->execute('UPDATE article SET hits = "'.$hits['hits'].'" WHERE id = '.$article['id']);
        }

        $this->execute('ALTER TABLE `article_author` DROP `id`');

        $table = $this->table('topic');
        $table->addColumn('name', 'string', array('limit' => 255))
              ->addIndex('name', array('unique' => true))
              ->create();

        $table = $this->table('article_topic', array('id' => false, 'primary_key' => array('article_id', 'topic_id')));
        $table->addColumn('article_id', 'integer')
              ->addColumn('topic_id', 'integer')
              ->addForeignKey('article_id', 'article', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('topic_id', 'topic', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->create();
    }
}
