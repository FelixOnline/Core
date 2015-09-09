<?php

use Phinx\Migration\AbstractMigration;

class ArchiveSetting extends AbstractMigration
{
    public function up()
    {
        $this->execute('INSERT INTO settings VALUES("archive_location", "Root of the issue archive PDF store, include trailing slash", "/website/media/felix/archive/");');
    }

    public function down()
    {
        $this->execute('DELETE FROM settings WHERE setting = "archive_location"');
    }
}
