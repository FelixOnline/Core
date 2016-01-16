<?php

use Phinx\Migration\AbstractMigration;

class AuditLog extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        $table = $this->table('audit_log');
        $table->addColumn('timestamp', 'datetime')
              ->addColumn('table', 'string')
              ->addColumn('key', 'string')
              ->addColumn('user', 'string')
              ->addColumn('action', 'string')
              ->addColumn('fields', 'text')

              ->addIndex(array('table', 'key'))
              ->addIndex(array('user'))
              ->addIndex(array('timestamp'))
              ->addColumn('deleted', 'boolean', array('default' => 0))
              ->create();
    }
}
