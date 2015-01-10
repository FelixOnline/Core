<?php

use Phinx\Migration\AbstractMigration;

class FixCommentReplies extends AbstractMigration
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
        $this->execute('UPDATE `comment` SET `reply` = NULL WHERE `reply` = 0');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('UPDATE `comment` SET `reply` = 0 WHERE `reply` IS NULL');
    }
}