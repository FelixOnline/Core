<?php

use Phinx\Migration\AbstractMigration;

class ArticleReviewedBy extends AbstractMigration
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
    public function up()
    {
        $table = $this->table('article');

        $table->addColumn('reviewedby', 'string', array('length' => 16, 'null' => true))
              ->addForeignKey('reviewedby', 'user', 'user', array('delete'=> 'RESTRICT', 'update'=> 'CASCADE'))
              ->save();

        foreach($this->fetchAll('SELECT * FROM article WHERE hidden = 0') as $article) {
            $this->execute('UPDATE article SET reviewedby = "felix" WHERE id = '.$article['id']);
        }
    }

    public function down()
    {
        $table = $this->table('article');

        $table->dropForeignKey('reviewedby')
              ->removeColumn('reviewedby')
              ->save();
    }
}
