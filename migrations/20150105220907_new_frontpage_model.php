<?php

use Phinx\Migration\AbstractMigration;

class NewFrontpageModel extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
    */
    
    /**
     * Migrate Up.
     */
    public function up()
    {
        $articles = array();

        $items = $this->query('SELECT * FROM frontpage');
        foreach ($items as $item) {
            $section = $item['section'];
            foreach($item as $key => $id) {
                if($key == 'layout' || $key == 'section' || $key < 1 || $key > 8 || $id == 0 || $id == NULL) {
                    continue;
                }

                $articles[] = array('article' => $id, 'section' => $section);
            }
        }

        // Data backed up to memory, now recreate the table

        $this->dropTable('frontpage');

        $table = $this->table('frontpage');
        $table->addColumn('article', 'integer')
              ->addColumn('section', 'string')
              ->addColumn('sort_order', 'integer')
              ->addForeignKey('article', 'article', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        // Now import data
        foreach($articles as $article) {
            $this->execute('INSERT INTO frontpage (article, section, sort_order) VALUES('.$article['article'].', "'.$article['section'].'", 0);');
        }
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        echo "NOTICE: If there are more than 8 articles in a front page section, the 9th article onwards will be dropped. This rollback is not able to restore the original order, it will restore order based on the sort order currently specified.";
        $articles = array();

        $items = $this->query('SELECT * FROM frontpage ORDER BY sort_order ASC');

        foreach ($items as $item) {
            $section = $item['section'];
            if(!array_key_exists($section, $articles)) {
                $articles[$section] = array();
            }

            if(count($articles[$section]) == 8) {
                continue;
            }

            $articles[$section][] = $item['article'];
        }

        // Buld up SQL
        $sql = array();
        foreach($articles as $section => $items) {
            $count = count($items);
            $sql[$section] = implode(', ', $items);

            while($count < 8) {
                $sql[$section] .= ', NULL';
                $count++;
            }
        }

        // Data backed up to memory, now recreate the table

        $this->dropTable('frontpage');

        $table = $this->table('frontpage', array('id' => false));
        $table->addColumn('layout', 'integer')
              ->addColumn('section', 'string')
              ->addColumn('1', 'integer', array('null' => true))
              ->addColumn('2', 'integer', array('null' => true))
              ->addColumn('3', 'integer', array('null' => true))
              ->addColumn('4', 'integer', array('null' => true))
              ->addColumn('5', 'integer', array('null' => true))
              ->addColumn('6', 'integer', array('null' => true))
              ->addColumn('7', 'integer', array('null' => true))
              ->addColumn('8', 'integer', array('null' => true))
              ->save();

        // Now import data
        foreach($sql as $section => $query) {
            $this->execute('INSERT INTO frontpage (layout, section, `1`, `2`, `3`, `4`, `5`, `6`, `7`, `8`) VALUES(1, "'.$section.'", '.$query.');');
        }
    }
}