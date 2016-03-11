<?php

use Phinx\Migration\AbstractMigration;

class Ipv6Support extends AbstractMigration
{
    private $tables = array('article_visit', 'comment', 'login', 'user');

    public function up()
    {
        foreach($this->tables as $table) {
            $this->execute('ALTER TABLE '.$table.' MODIFY ip VARCHAR(255);');
        }
    }

    public function down()
    {
        foreach($this->tables as $table) {
            $this->execute('ALTER TABLE '.$table.' MODIFY ip VARCHAR(25);'); // They were a mix before but ave 25.
        }
    }
}
