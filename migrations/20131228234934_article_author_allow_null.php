<?php

use Phinx\Migration\AbstractMigration;

class ArticleAuthorAllowNull extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
		$this->execute('ALTER TABLE article MODIFY author VARCHAR(16)');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		$this->execute('ALTER TABLE article MODIFY author VARCHAR(16) NOT NULL');
    }
}
