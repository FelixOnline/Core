<?php

use Phinx\Migration\AbstractMigration;

class RoleMigration extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('roles');
        $table->addColumn('name', 'string', array('limit' => 25))
              ->addColumn('description', 'string', array('limit' => 150))
              ->addColumn('parent', 'integer', array('null' => true))
              ->addIndex(array('parent'))
              ->create();
        $this->execute("ALTER TABLE `roles` ADD FOREIGN KEY (`parent`) REFERENCES `roles`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;");

        $table = $this->table('user_roles');
        $table->addColumn('user', 'string', array('limit' => 16))
              ->addColumn('role', 'integer')
              ->addIndex(array('role'))
              ->addIndex(array('user'))
              ->addForeignKey('user', 'user', 'user', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->addForeignKey('role', 'roles', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->create();

        $this->execute('INSERT INTO roles VALUES(NULL, "superUser", "Full administrative access", NULL);');

        // Migrate superuser roles
        foreach($this->fetchAll('SELECT * FROM user WHERE role = 100') as $user) {
            $this->execute('INSERT INTO user_roles VALUES(NULL, "'.$user['user'].'", 1);'); // We assume newly inserted role is id 1
        }

        // Cleanup
        $table = $this->table('user');
        $table->dropForeignKey('role')->save();
        $table->removeColumn('role')->save();

        $this->dropTable('role');

        echo "Please note only superuser roles have been migrated to the new format.\n";
    }

    public function down()
    {
        $table = $this->table('role');
        $table->addColumn('role', 'string', array('limit' => 255))
              ->create();

        $this->execute('INSERT INTO role VALUES
            (1, "User"),
            (10, "Author"),
            (20, "Section Editor"),
            (25, "Web Editor"),
            (30, "Senior Editor"),
            (100, "Super User");

            UPDATE role SET id = 0 WHERE id = 1'); // Fix

        $table = $this->table('user');
        $table->addColumn('role', 'integer')
              ->addIndex(array('role'))
              ->addForeignKey('role', 'role', 'id', array('delete'=> 'CASCADE', 'update'=> 'CASCADE'))
              ->save();

        // Migrate superuser roles
        foreach($this->fetchAll('SELECT * FROM user_roles WHERE role = 1') as $user) {
            $this->execute('UPDATE user SET role = 100 WHERE user = "'.$user['user'].'";');
        }

        $this->dropTable('user_roles');

        $table = $this->table('roles');
        $table->dropForeignKey('parent')->save();

        $this->dropTable('roles');

        echo "Please note only superuser roles have been migrated to the old format.\n";
    }
}
