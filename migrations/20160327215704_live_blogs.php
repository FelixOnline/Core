<?php

use Phinx\Migration\AbstractMigration;

class LiveBlogs extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('article');
        $table->addColumn('is_live', 'boolean')
              ->addColumn('blog', 'integer', array('null' => true))
              ->addForeignKey('blog', 'blogs', 'id', array('update' => 'CASCADE', 'delete' => 'SET_NULL'))
              ->save();

        $table = $this->table('blogs');
        $table->addColumn('sprinkler_prefix', 'string', array('length' => 255, 'null' => false))
              ->removeColumn('name')
              ->removeColumn('slug')
              ->removeColumn('controller')
              ->removeColumn('sticky')
              ->save();

        $table = $this->table('blog_post');
        $table->addColumn('title', 'string', array('length' => 500, 'null' => false))
              ->addColumn('breaking', 'boolean')
              ->removeColumn('type')
              ->removeColumn('meta')
              ->removeColumn('visible')
              ->save();

        $this->execute('INSERT INTO settings (setting, description, value) VALUES("sprinkler_host", "Hostname/IP for the Sprinker server (no http[s])", "0.0.0.0");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("sprinkler_port", "Sprinkler port number", "3000");');
        $this->execute('INSERT INTO settings (setting, description, value) VALUES("sprinkler_admin", "Sprinkler admin key", "");');
    }

    public function down()
    {
        $table = $this->table('article');
        $table->dropForeignKey('blog')
              ->removeColumn('is_live')
              ->removeColumn('blog')
              ->save();

        $table = $this->table('blogs');
        $table->addColumn('name', 'string', array('length' => 30))
              ->addColumn('slug', 'string', array('length' => 30))
              ->addColumn('controller', 'string', array('length' => 30))
              ->addColumn('sticky', 'text')
              ->removeColumn('sprinkler_prefix')
              ->save();

        $table = $this->table('blog_post');
        $table->removeColumn('title')
              ->removeColumn('breaking')
              ->addColumn('type', 'string', array('length' => 255, 'null' => false))
              ->addColumn('meta', 'text')
              ->addColumn('visible', 'boolean')
              ->save();

        $this->execute('DELETE FROM settings WHERE setting = "sprinkler_host"');
        $this->execute('DELETE FROM settings WHERE setting = "sprinkler_port"');
        $this->execute('DELETE FROM settings WHERE setting = "sprinkler_admin"');
    }
}
