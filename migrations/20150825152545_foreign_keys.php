<?php

use Phinx\Migration\AbstractMigration;

class ForeignKeys extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('article_visit');
        $table->addForeignKey('user', 'user', 'user', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->save();

        $this->execute("ALTER TABLE blogs CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
        $this->execute("ALTER TABLE blogs ENGINE=InnoDB;");

        $this->execute("ALTER TABLE blog_post CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
        $this->execute("ALTER TABLE blog_post ENGINE=InnoDB;");

        $table = $this->table('blog_post');
        $table->addForeignKey('blog', 'blogs', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('author', 'user', 'user', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('category');
        $table->addForeignKey('parent', 'category', 'id', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('comment_like');
        $table->addForeignKey('comment', 'comment', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('cookies');
        $table->addForeignKey('user', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('image');
        $table->addForeignKey('user', 'user', 'user', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('login');
        $table->addForeignKey('user', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('notices');
        $table->addForeignKey('author', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();
    }

    public function down()
    {
        $table = $this->table('article_visit');
        $table->dropForeignKey('article')
              ->dropForeignKey('user')
              ->save();

        $table = $this->table('blog_post');
        $table->dropForeignKey('blog')
              ->dropForeignKey('author')
              ->save();

        $this->execute("ALTER TABLE blogs ENGINE=MyISAM;");
        $this->execute("ALTER TABLE blog_post ENGINE=MyISAM;");
        $this->execute("ALTER TABLE `blog_post` CHANGE `author` `author` VARCHAR(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;");

        $table = $this->table('category');
        $table->dropForeignKey('parent')
              ->save();

        $table = $this->table('comment_like');
        $table->dropForeignKey('comment')
              ->dropForeignKey('user')
              ->save();

        $table = $this->table('cookies');
        $table->dropForeignKey('comment')
              ->save();

        $table = $this->table('image');
        $table->dropForeignKey('user')
              ->save();

        $table = $this->table('login');
        $table->dropForeignKey('user')
              ->save();

        $table = $this->table('notices');
        $table->dropForeignKey('author')
              ->save();
    }
}
