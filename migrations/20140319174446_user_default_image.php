<?php

use Phinx\Migration\AbstractMigration;

class UserDefaultImage extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
		$this->execute('ALTER TABLE user MODIFY image INT(11) NOT NULL DEFAULT 676');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		$this->execute('ALTER TABLE user MODIFY image INT(11) NOT NULL');
    }
}
