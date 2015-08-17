<?php

use Phinx\Migration\AbstractMigration;

class CategoryAuthorTweaks extends AbstractMigration
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
        $this->execute('ALTER TABLE `category_author` ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);');
        $this->execute('ALTER TABLE `category_author` DROP `admin`');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('ALTER TABLE `category_author` ADD `admin` BOOLEAN NOT NULL DEFAULT TRUE COMMENT \'Section editor\';');
        $this->execute('ALTER TABLE `category_author` DROP `id`');
    }

}
