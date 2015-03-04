<?php

use Phinx\Migration\AbstractMigration;

class ChangePollLocation extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute("ALTER TABLE `polls` CHANGE `bottom` `location` INT(6) NOT NULL DEFAULT '0'");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute("ALTER TABLE `polls` CHANGE `location` `bottom` INT(1) NOT NULL DEFAULT '0'");
    }
}