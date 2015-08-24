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

            $this->execute('INSERT INTO email_validation (id, email, code, confirmed) VALUES(NULL, "'.$email.'", "", 1)');
        }

        // Add existing email addresses from users
        $migratedEmails = array();

        foreach($this->fetchAll('SELECT email FROM user') as $email) {
            $email = strtolower($email['email']);

            if($email == '') {
                continue;
            }

            if(array_search($email, $migratedEmails)) {
                continue;
            }

            $migratedEmails[] = $email;

            $this->execute('INSERT INTO email_validation (id, email, code, confirmed) VALUES(NULL, "'.$email.'", "", 1)');
        }
    }

    public function down() {
        $this->dropTable('email_validation');
    }
}
