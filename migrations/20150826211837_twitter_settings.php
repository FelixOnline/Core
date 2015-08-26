<?php

use Phinx\Migration\AbstractMigration;

class TwitterSettings extends AbstractMigration
{
    public function up()
    {
        $this->execute('INSERT INTO settings VALUES("twitter_key", "Application API key from Twitter for admin article editor", "");');
        $this->execute('INSERT INTO settings VALUES("twitter_secret", "Application API secret from Twitter for admin article editor", "");');
    }

    public function down()
    {
        $this->execute('DELETE FROM settings WHERE key = "twitter_key"');
        $this->execute('DELETE FROM settings WHERE key = "twitter_secret"');
    }
}
