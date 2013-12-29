<?php

use Phinx\Migration\AbstractMigration;

class TextStoryUserNull extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
		$this->execute('ALTER TABLE text_story MODIFY user VARCHAR(16)');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		$this->execute('ALTER TABLE text_story MODIFY user VARCHAR(16) NOT NULL');
    }
}
