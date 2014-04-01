<?php

use Phinx\Migration\AbstractMigration;

class AkismetLog extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $this->execute('
          CREATE TABLE IF NOT EXISTS `akismet_log` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `comment_id` int(11) NOT NULL,
            `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `action` varchar(10) NOT NULL,
            `is_spam` tinyint(1) NOT NULL,
            `error` text NULL,
            `request` text NULL,
            `response` text NULL,
            PRIMARY KEY (`id`),
            KEY `comment_id` (`comment_id`)
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


          ALTER TABLE `akismet_log`
            ADD CONSTRAINT `comment_link` FOREIGN KEY (`comment_id`) REFERENCES `comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
        ');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->execute('
            DROP TABLE `akismet_log`
        ');
    }
}
