<?php

use Phinx\Migration\AbstractMigration;

class ForeignKeys extends AbstractMigration
{
    public function up()
    {
        // Delete authorships for missing authors
        foreach($this->fetchAll('SELECT DISTINCT author FROM article_author') as $user) {
            $count = $this->fetchAll('SELECT * FROM user WHERE user = "'.$user['author'].'"');

            if(count($count) == 0) {
                $this->execute("DELETE FROM article_author WHERE author = '".$user['author']."'");
            }
        }

        $table = $this->table('article_author');
        $table->addForeignKey('article', 'article', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('author', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('article_polls');
        $table->addForeignKey('article', 'article', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('poll', 'polls', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        // Set null any visits relating to missing users
        foreach($this->fetchAll('SELECT DISTINCT user FROM article_visit') as $user) {
            $count = $this->fetchAll('SELECT * FROM user WHERE user = "'.$user['user'].'"');

            if(count($count) == 0) {
                $this->execute("UPDATE article_visit SET user = NULL WHERE user = '".$user['user']."'");
            }
        }

        $table = $this->table('article_visit');
        $table->addForeignKey('article', 'article', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('user', 'user', 'user', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->save();

        $this->execute("ALTER TABLE blogs CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
        $this->execute("ALTER TABLE blogs ENGINE=InnoDB;");

        $this->execute("ALTER TABLE blog_post CONVERT TO CHARACTER SET utf8 COLLATE utf8_unicode_ci;");
        $this->execute("ALTER TABLE blog_post ENGINE=InnoDB;");

        // Assess whether the users exist before we migrate
        foreach($this->fetchAll('SELECT DISTINCT author FROM blog_post') as $user) {
            $count = $this->fetchAll('SELECT * FROM user WHERE user = "'.$user['author'].'"');

            if(count($count) == 0) {
                echo $user['author']."\n";
            }
        }

        $table = $this->table('blog_post');
        $table->addForeignKey('blog', 'blogs', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              //->addForeignKey('author', 'user', 'user', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('category');
        $table->addForeignKey('parent', 'category', 'id', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->save();

        // Set null any comments relating to missing users
        foreach($this->fetchAll('SELECT DISTINCT user FROM category_author') as $user) {
            $count = $this->fetchAll('SELECT * FROM user WHERE user = "'.$user['user'].'"');

            if(count($count) == 0) {
                $this->execute("DELETE FROM category_author WHERE user = '".$user['user']."'");
            }
        }


        $table = $this->table('category_author');
        $table->addForeignKey('category', 'category', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('user', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        // Delete any comments with ID of 0
        $this->execute("DELETE FROM comment WHERE article = 0");

        // Set null any comments relating to missing users
        foreach($this->fetchAll('SELECT DISTINCT user FROM comment') as $user) {
            $count = $this->fetchAll('SELECT * FROM user WHERE user = "'.$user['user'].'"');

            if(count($count) == 0) {
                $this->execute("UPDATE comment SET user = NULL WHERE user = '".$user['user']."'");
            }
        }

        $table = $this->table('comment');
        $table->addForeignKey('article', 'article', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('user', 'user', 'user', array('delete'=> 'SET_NULL', 'update'=> 'CASCADE'))
              ->save();

        // Delete likes relating to missing users
        foreach($this->fetchAll('SELECT DISTINCT user FROM comment_like') as $user) {
            $count = $this->fetchAll('SELECT * FROM user WHERE user = "'.$user['user'].'"');

            if(count($count) == 0) {
                $this->execute("DELETE FROM comment_like WHERE user = '".$user['user']."'");
            }
        }

        // Delete likes relating to missing comments
        foreach($this->fetchAll('SELECT DISTINCT comment FROM comment_like') as $comment) {
            $count = $this->fetchAll('SELECT * FROM comment WHERE id = "'.$comment['comment'].'"');

            if(count($count) == 0) {
                $this->execute("DELETE FROM comment_like WHERE comment = '".$comment['comment']."'");
            }
        }

        $table = $this->table('comment_like');
        $table->addForeignKey('comment', 'comment', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('user', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        // Delete cookies relating to missing users
        foreach($this->fetchAll('SELECT DISTINCT user FROM cookies') as $user) {
            $count = $this->fetchAll('SELECT * FROM user WHERE user = "'.$user['user'].'"');

            if(count($count) == 0) {
                $this->execute("DELETE FROM cookies WHERE user = '".$user['user']."'");
            }
        }

        $table = $this->table('cookies');
        $table->addForeignKey('user', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('frontpage');
        $table->addForeignKey('article', 'article', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        // Set to felix any images relating to missing users
        foreach($this->fetchAll('SELECT DISTINCT user FROM image') as $user) {
            $count = $this->fetchAll('SELECT * FROM user WHERE user = "'.$user['user'].'"');

            if(count($count) == 0) {
                $this->execute("UPDATE image SET user = 'felix' WHERE user = '".$user['user']."'");
            }
        }

        $table = $this->table('image');
        $table->addForeignKey('user', 'user', 'user', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'))
              ->save();

        // Delete cookies relating to missing users
        foreach($this->fetchAll('SELECT DISTINCT user FROM login') as $user) {
            $count = $this->fetchAll('SELECT * FROM user WHERE user = "'.$user['user'].'"');

            if(count($count) == 0) {
                $this->execute("DELETE FROM login WHERE user = '".$user['user']."'");
            }
        }

        $table = $this->table('login');
        $table->addForeignKey('user', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        // Set to felix any notices relating to missing users
        foreach($this->fetchAll('SELECT DISTINCT author FROM notices') as $user) {
            $count = $this->fetchAll('SELECT * FROM user WHERE user = "'.$user['author'].'"');

            if(count($count) == 0) {
                $this->execute("UPDATE notices SET author = 'felix' WHERE author = '".$user['author']."'");
            }
        }

        $table = $this->table('notices');
        $table->addForeignKey('author', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        // Set to felix any polls relating to missing users
        foreach($this->fetchAll('SELECT DISTINCT author FROM polls') as $user) {
            $count = $this->fetchAll('SELECT * FROM user WHERE user = "'.$user['author'].'"');

            if(count($count) == 0) {
                $this->execute("UPDATE polls SET author = 'felix' WHERE author = '".$user['author']."'");
            }
        }

        $table = $this->table('polls');
        $table->addForeignKey('author', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('polls_option');
        $table->addForeignKey('poll', 'polls', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        $table = $this->table('polls_response');
        $table->addForeignKey('poll', 'polls', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('option', 'polls_option', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        // Set to felix any texts relating to missing users
        foreach($this->fetchAll('SELECT DISTINCT user FROM text_story') as $user) {
            $count = $this->fetchAll('SELECT * FROM user WHERE user = "'.$user['user'].'"');

            if(count($count) == 0) {
                $this->execute("UPDATE text_story SET user = 'felix' WHERE user = '".$user['user']."'");
            }
        }

        $table = $this->table('text_story');
        $table->addForeignKey('user', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();
    }

    public function down()
    {
        $table = $this->table('article_author');
        $table->dropForeignKey('article')
              ->dropForeignKey('author')
              ->save();

        $table = $this->table('article_polls');
        $table->dropForeignKey('article')
              ->dropForeignKey('poll')
              ->save();

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

        $table = $this->table('category_author');
        $table->dropForeignKey('category')
              ->dropForeignKey('user')
              ->save();

        $table = $this->table('comment');
        $table->dropForeignKey('article')
              ->dropForeignKey('user')
              ->save();

        $table = $this->table('comment_like');
        $table->dropForeignKey('comment')
              ->dropForeignKey('user')
              ->save();

        $table = $this->table('cookies');
        $table->dropForeignKey('comment')
              ->save();

        $table = $this->table('frontpage');
        $table->dropForeignKey('article')
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

        $table = $this->table('polls');
        $table->dropForeignKey('author')
              ->save();

        $table = $this->table('polls_option');
        $table->dropForeignKey('poll')
              ->save();

        $table = $this->table('polls_response');
        $table->dropForeignKey('poll')
              ->dropForeignKey('option')
              ->save();

        $table = $this->table('text_story');
        $table->dropForeignKey('user')
              ->save();
    }
}
