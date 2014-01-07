<?php

use Phinx\Migration\AbstractMigration;

class NullDefaultImages extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
		$this->execute('UPDATE `article` SET img1 = NULL WHERE `img1` IN (183, 742)');
    }

    /**
     * Migrate Down.
	 *
	 * CAN'T REVERT
     */
    public function down()
    {

    }
}
