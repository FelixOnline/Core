<?php

use Phinx\Migration\AbstractMigration;

class EmailValidation extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('email_validation');
        $table->addColumn('email', 'string', array('limit' => 500))
              ->addColumn('code', 'string', array('limit' => 13))
              ->addColumn('confirmed', 'boolean')
              ->addIndex(array('email'), array('unique' => true))
              ->addIndex(array('email', 'confirmed'))
              ->create();

        // Add existing email addresses from comments
        $migratedEmails = array();

        foreach($this->fetchAll('SELECT email FROM comment WHERE active = 1 AND spam = 0 AND pending = 0') as $email) {
            $email = strtolower($email['email']);

            if($email == '') {
                continue;
            }

            if(array_search($email, $migratedEmails)) {
                continue;
            }

            $migratedEmails[] = $email;

            try {
                $this->execute('INSERT INTO email_validation (id, email, code, confirmed) VALUES(NULL, "'.$email.'", "", 1)');
            } catch(\Exception $e) {
                // Don't do anything if insert fails as we'll just have to get them to validate their email unless its a double entry issue
            }
        }

        // Add existing email addresses from users

        foreach($this->fetchAll('SELECT email FROM user') as $email) {
            $email = strtolower($email['email']);

            if($email == '') {
                continue;
            }

            if(array_search($email, $migratedEmails)) {
                continue;
            }

            $migratedEmails[] = $email;

            try {
                $this->execute('INSERT INTO email_validation (id, email, code, confirmed) VALUES(NULL, "'.$email.'", "", 1)');
            } catch(\Exception $e) {
                // Don't do anything if insert fails as we'll just have to get them to validate their email unless its a double entry issue
            }
        }
    }

    public function down() {
        $this->dropTable('email_validation');
    }
}
