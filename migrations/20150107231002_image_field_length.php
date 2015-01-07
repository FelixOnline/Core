<?php

use Phinx\Migration\AbstractMigration;

class ImageFieldLength extends AbstractMigration
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
        $this->execute('ALTER TABLE `image` CHANGE `caption` `caption` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');
        $this->execute('ALTER TABLE `image` CHANGE `attribution` `attribution` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');
        $this->execute('ALTER TABLE `image` CHANGE `attr_link` `attr_link` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('ALTER TABLE `image` CHANGE `caption` `caption` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');
        $this->execute('ALTER TABLE `image` CHANGE `attribution` `attribution` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');
        $this->execute('ALTER TABLE `image` CHANGE `attr_link` `attr_link` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;');
    }
}