<?php

use Phinx\Migration\AbstractMigration;

class CommentConsolidation2 extends AbstractMigration
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
        echo "Building list of user/email adjustments\n";
        $items = $this->query('SELECT * FROM comment WHERE external = 0');
        $sql_to_exec = array();

        foreach ($items as $item) {
            $user = $this->query('SELECT * FROM user WHERE user = "'.$item['user'].'";');

            if($user->rowCount() == 0) {
                $this->execute("INSERT INTO `user` (`user`, `name`, `visits`, `ip`, `timestamp`, `role`, `info`, `description`, `email`, `facebook`, `twitter`, `websitename`, `websiteurl`, `image`) VALUES ('".$item['user']."', '".$item['user']."', '0', '0.0.0.0', CURRENT_TIMESTAMP, '0', '', NULL, '".$item['user']."@imperial.ac.uk', NULL, NULL, NULL, NULL, '676');");
            } elseif($user->rowCount() != 1) {
                die('Too many users found '.$item['user']);
            }

            $user = $user->fetch();

            $name = $user['name'];
            if(strtolower($item['user']) == 'felix') {
                $name = 'Felix Editor'; // prevent misattribution
            }

            $sql_to_exec[] = 'UPDATE comment SET name = "'.$name.'", email = "'.$user['email'].'" WHERE id = '.$item['id'].';';
        }

        echo "Running queries\n";
        foreach($sql_to_exec as $query) {
            $this->execute($query);
        }

        echo "Tidying up\n";
        $this->execute('ALTER TABLE `comment` DROP COLUMN `external`');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        echo "Please note that the data change in this migration cannot be reversed. Reversing structure changes";
        $table = $this->table('comment');
        $table->addColumn('external', 'boolean', array(
            'null' => false,
            'after' => 'article',
            'default' => 0,
        ));
        $table->save();
    }
}