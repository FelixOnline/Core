<?php

use Phinx\Migration\AbstractMigration;

class ArticleDefaults extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
		$this->execute('ALTER TABLE article MODIFY hidden TINYINT(1) NOT NULL DEFAULT 0');
		$this->execute('ALTER TABLE article MODIFY searchable TINYINT(1) NOT NULL DEFAULT 1');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		$this->execute('ALTER TABLE article MODIFY hidden TINYINT(1) NOT NULL');
		$this->execute('ALTER TABLE article MODIFY searchable TINYINT(1) NOT NULL');
    }
}
