<?php

use Phinx\Migration\AbstractMigration;

class CommentFieldLength extends AbstractMigration
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
        $this->execute('ALTER TABLE `comment` CHANGE `comment` `comment` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('ALTER TABLE `comment` CHANGE `comment` `comment` VARCHAR(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;');
    }
}