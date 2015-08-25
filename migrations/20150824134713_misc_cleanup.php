<?php

use Phinx\Migration\AbstractMigration;

class MiscCleanup extends AbstractMigration
{
    public function up()
    {
        // Some of these tables may not exist so use alternative way to handle migration
        $tables = array('engine_page', 'ffs_completers', 'ffs_responses', 'optimise', 'page', 'poll', 'poll_chart_type',
            'poll_option', 'poll_vote', 'preview_email', 'sport_table', 'text_global', 'text_story_bkp', 'thephig_albums',
            'thephig_images', 'thephig_info', 'thephig_users', 'top_2col', 'top_extrapage_cat');

        foreach($tables as $table) {
            try {
                $this->dropTable($table);
            } catch(Exception $e) {
                // continue
            }
        }
    }

    public function down()
    {
        $table = $this->table('engine_page');
        $table->addColumn('label', 'string', array('length' => 32))
              ->addColumn('uri', 'string', array('length' => 255))
              ->addColumn('inc', 'string', array('length' => 255, 'null' => true))
              ->addColumn('active', 'integer', array('length' => 4, 'default' => 1))
              ->addIndex(array('active'))
              ->create();

        $table = $this->table('ffs_completers', array('id' => false));
        $table->addColumn('uname', 'string', array('length' => 45))
              ->create();

        $table = $this->table('ffs_responses');
        $table->addColumn('data', 'text', array('null' => true))
              ->addColumn('deptcheck', 'integer', array('length' => 4, 'null' => true))
              ->create();

        $table = $this->table('optimise', array('id' => false));
        $table->addColumn('article', 'integer', array('length' => 10))
              ->addColumn('optimised', 'integer', array('length' => 11))
              ->create();

        $table = $this->table('page');
        $table->addColumn('label', 'string', array('length' => 15))
              ->addColumn('uri', 'string', array('length' => 500))
              ->create();

        $table = $this->table('poll');
        $table->addColumn('title', 'string', array('length' => 140))
              ->addColumn('article_id', 'integer', array('null' => true))
              ->addColumn('author', 'string', array('length' => 16))
              ->addColumn('description', 'string', array('length' => 1000, 'null' => true))
              ->addColumn('options', 'integer', array('default' => 1, 'comment' => '1 radio, 2+ checkbox'))
              ->addColumn('open', 'time', array('null' => true))
              ->addColumn('close', 'time', array('null' => true))
              ->addColumn('created', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
              ->addColumn('limit', 'integer', array('null' => true))
              ->addColumn('chart_type_id', 'integer')
              ->addIndex(array('article_id'))
              ->addIndex(array('author'))
              ->addIndex(array('chart_type_id'))
              ->create();

        $table = $this->table('poll_chart_type');
        $table->addColumn('type', 'string', array('length' => 32))
              ->create();

        $table = $this->table('poll_option');
        $table->addColumn('poll_id', 'integer')
              ->addColumn('option', 'integer')
              ->addColumn('label', 'string', array('length' => 140))
              ->addIndex(array('poll_id'))
              ->create();

        $table = $this->table('poll_vote');
        $table->addColumn('poll_id', 'integer')
              ->addColumn('user', 'string', array('length' => 16))
              ->addColumn('answer', 'integer')
              ->addColumn('timestamp', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
              ->addIndex(array('poll_id', 'user'))
              ->addIndex(array('user'))
              ->create();

        $table = $this->table('preview_email');
        $table->addColumn('email', 'string', array('length' => 50))
              ->addColumn('ip', 'string', array('length' => 15))
              ->create();

        $table = $this->table('sport_table');
        $table->addColumn('team', 'string', array('length' => 30))
              ->addColumn('played', 'integer')
              ->addColumn('won', 'integer')
              ->addColumn('drawn', 'integer')
              ->addColumn('lost', 'integer')
              ->addColumn('f', 'integer')
              ->addColumn('a', 'integer')
              ->addColumn('difference', 'float')
              ->addColumn('index', 'float')
              ->addColumn('win_percent', 'integer')
              ->create();

        $table = $this->table('text_global');
        $table->addColumn('key', 'integer', array('length' => 50))
              ->addColumn('value', 'string', array('length' => 5000, 'null' => true))
              ->addColumn('style', 'integer', array('length' => 50, 'null' => true))
              ->create();

        $table = $this->table('text_story_bkp');
        $table->addColumn('user', 'integer', array('length' => 16))
              ->addColumn('content', 'text', array('null' => true))
              ->addColumn('timestamp', 'timestamp', array('default' => 'CURRENT_TIMESTAMP'))
              ->addIndex(array('user'))
              ->create();

        echo "Please note that thepig tables will not be restored as these reside from a different piece of software.\n";

        $table = $this->table('top_2col');
        $table->addColumn('1', 'integer')
              ->addColumn('2', 'integer')
              ->addColumn('3', 'integer')
              ->addColumn('4', 'integer')
              ->addIndex(array('1'))
              ->addIndex(array('2'))
              ->addIndex(array('3'))
              ->addIndex(array('4'))
              ->create();

        $table = $this->table('top_extrapage_cat');
        $table->addColumn('cat1', 'integer')
              ->addColumn('cat2', 'integer')
              ->addColumn('cat3', 'integer')
              ->addColumn('cat4', 'integer')
              ->addColumn('cat5', 'integer')
              ->addColumn('loc', 'string', array('length' => 16))
              ->addIndex(array('cat1'))
              ->addIndex(array('cat2'))
              ->addIndex(array('cat3'))
              ->addIndex(array('cat4'))
              ->addIndex(array('cat5'))
              ->addIndex('loc', array('unique' => true))
              ->create();
    }
}
