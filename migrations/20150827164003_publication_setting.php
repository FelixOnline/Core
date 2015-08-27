<?php

use Phinx\Migration\AbstractMigration;

class PublicationSetting extends AbstractMigration
{
    public function up()
    {
        $this->execute('INSERT INTO settings VALUES("frontpage_publication", "ID number of the issue archive publication to show the latest issue of on the front page.", "1");');
    }

    public function down()
    {
        $this->execute('DELETE FROM settings WHERE setting = "frontpage_publication"');
    }
}
