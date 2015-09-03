<?php

use Phinx\Migration\AbstractMigration;

class SidebarAds extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     */
    public function change()
    {
        $ads = $this->table('advert');
        $ads->addColumn('sidebar', 'boolean', array('default' => 0))
            ->save();
    }
}
