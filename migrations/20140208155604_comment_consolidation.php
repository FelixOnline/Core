<?php

use Phinx\Migration\AbstractMigration;

class CommentConsolidation extends AbstractMigration
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
		$table = $this->table('comment');
        $table->addColumn('external', 'boolean', array(
            'null' => false,
            'after' => 'article',
			'default' => 0,
		))
		->addColumn('pending', 'boolean', array(
            'null' => false,
            'after' => 'active',
			'default' => 0,
			'comment' => 'Pending approval',
        ))
        ->addColumn('spam', 'boolean', array(
            'null' => false,
            'after' => 'active',
			'default' => 0,
        ))
        ->addColumn('name', 'string', array(
            'null' => true,
            'after' => 'user',
            'length' => 32,
        ))
        ->addColumn('ip', 'string', array(
            'null' => true,
            'after' => 'timestamp',
            'length' => 30,
        ))
        ->addColumn('referer', 'string', array(
            'null' => true,
            'after' => 'ip',
            'length' => 300,
        ))
        ->addColumn('useragent', 'string', array(
            'null' => true,
            'after' => 'referer',
            'length' => 300,
        ))
        ->addColumn('email', 'string', array(
            'null' => true,
            'after' => 'useragent',
            'length' => 300,
        ))
		->addIndex(array('name'))
		->addIndex(array('pending'))
        ->save(); 
    }
}
