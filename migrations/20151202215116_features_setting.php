<?php

use Phinx\Migration\AbstractMigration;

class FeaturesSetting extends AbstractMigration
{
    public function up()
    {
        $this->execute('INSERT INTO settings VALUES("features_category_id", "(For the front page) ID number of category: Features", "21");');
    }

    public function down()
    {
        $this->execute('DELETE FROM settings WHERE setting = "features_category_id"');
    }
}
