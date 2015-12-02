<?php

use Phinx\Migration\AbstractMigration;

class ArchiveUrlSetting extends AbstractMigration
{
    public function up()
    {
        $this->execute('INSERT INTO settings VALUES("archive_url_location", "URL to the issue archive PDF store, EXCLUDING trailing slash", "http://www.felixonline.co.uk/archive");');
    }

    public function down()
    {
        $this->execute('DELETE FROM settings WHERE setting = "archive_url_location"');
    }
}
