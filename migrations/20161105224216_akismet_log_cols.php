<?php

use Phinx\Migration\AbstractMigration;

class AkismetLogCols extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
        $table = $this->table('akismet_log');
        $table->removeColumn('request')
              ->removeColumn('response')
              ->save();
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $table = $this->table('akismet_log');
        $table->addColumn('request', 'text')
              ->addColumn('response', 'text')
              ->save();
    }
}
