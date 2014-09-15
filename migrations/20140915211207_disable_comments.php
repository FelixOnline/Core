<?php

use Phinx\Migration\AbstractMigration;

class DisableComments extends AbstractMigration
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
		$table = $this->table('article');
        $table->addColumn('comment_status', 'integer', array(
            'null' => false,
			'default' => 1,
		))
        ->save(); 

		$this->execute('
			UPDATE
				article
			SET comment_status = 0
			WHERE published < date_sub(NOW(), interval 1 month)
		'); 
    }
}
