<?php

use Phinx\Migration\AbstractMigration;

class UserEmails extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
		$this->execute('UPDATE user SET email = CONCAT(TRIM(user), "@imperial.ac.uk") WHERE email IS NULL'); 
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		// no going back
    }
}
