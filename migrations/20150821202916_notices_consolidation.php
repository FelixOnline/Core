<?php

use Phinx\Migration\AbstractMigration;

class NoticesConsolidation extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('notices');
        $table->addColumn('content', 'text', array("after" => "author"));
        $table->update();

        foreach($this->fetchAll('SELECT * FROM notices') as $notice) {
            $text = $this->fetchRow('SELECT * FROM text_story WHERE id = '.$notice['text']);
            $this->execute('UPDATE notices SET content = "'.str_replace('"', '\"', $text['content']).'" WHERE id = '.$notice['id']);

            if($text['converted']) {
                echo "WARNING! Text ".$text['id']." has been converted to Sir Trevor format and therefore will not render properly in the notice block. This will also prevent you from reversing this migration until you manually convert the notice back to plain HTML.\n";
            }
        }

        $table->dropForeignKey('text')->removeColumn('text')->update();

        echo "It is now safe to run the to-sir-trevor.php script\n";
    }

    public function down()
    {
        $table = $this->table('notices');
        $table->addColumn('text', 'integer', array('null' => true, "after" => "author"));
        $table->addForeignKey('text', 'text_story', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'));
        $table->update();

        foreach($this->fetchAll('SELECT * FROM notices') as $notice) {
            $this->execute('INSERT INTO text_story (id, user, content, timestamp, converted) VALUES(NULL, "'.$notice['author'].'", "'.str_replace('"', '\"', $notice['content']).'", "'.$notice['start_time'].'", 0);');

            $id = $this->adapter->getConnection()->lastInsertId();

            $this->execute('UPDATE notices SET text = '.$id.' WHERE id = '.$notice['id']);
        }

        $table->removeColumn('content')->update();

        echo "You should now run the to-sir-trevor.php script UNLESS you plan on re-applying this migration.\n";
    }
}
