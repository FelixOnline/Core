<?php

use Phinx\Migration\AbstractMigration;

class IssueArchive extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('archive_publication');

        $table->addColumn('name', 'string', array('length' => 40))
              ->addColumn('inactive', 'boolean', array('default' => 0))
              ->addIndex('inactive')
              ->create();

        $table = $this->table('archive_issue');

        $table->addColumn('issue', 'integer')
              ->addColumn('date', 'date')
              ->addColumn('publication', 'integer')
              ->addColumn('inactive', 'boolean', array('default' => 0))
              ->addIndex('publication')
              ->addIndex('issue')
              ->addIndex('inactive')
              ->addIndex(array('issue', 'publication'), array('unique' => true))
              ->addForeignKey('publication', 'archive_publication', 'id')
              ->create();

        $table = $this->table('archive_file');

        $table->addColumn('issue', 'integer')
              ->addColumn('issue_id', 'integer', array('null' => true))
              ->addColumn('publication', 'integer')
              ->addColumn('part', 'string', array('length' => 5))
              ->addColumn('filename', 'string', array('length' => 255))
              ->addColumn('content', 'text')
              ->addIndex('part')
              ->create();

        $this->execute("ALTER TABLE `archive_file` ADD FULLTEXT(`content`);");

        $db = readline("Please enter the name of the archive database to copy data from. Phinx must have read access to this database. To skip this step, press CTRL-D.\n");

        if(!$db) { return; }

        $this->execute("INSERT INTO archive_publication (id, name) SELECT PubNo AS id, PubName AS name FROM `".$db."`.Publications");

        $this->execute("INSERT INTO archive_issue (id, issue, date, publication) SELECT id AS id, IssueNo AS issue, PubDate AS date, PubNo AS publication FROM `".$db."`.Issues");

        $this->execute("INSERT INTO archive_file (issue, publication, part, filename, content) SELECT DISTINCT IssueNo AS issue, PubNo AS publication, Title AS part, FileName AS filename, Content AS content FROM `".$db."`.Files");

        // Now correct issue IDs
        foreach($this->fetchAll('SELECT id, issue, publication FROM archive_file') as $file) {
            $issue = $this->fetchRow('SELECT id FROM archive_issue WHERE issue = '.$file['issue'].' AND publication = '.$file['publication']);

            if($issue['id'] == NULL) {
                echo '-> Issue record could not be found for file '.$file['id'].' supposed issue '.$file['issue'].' in publication '.$file['publication']."\n";
                $this->execute('UPDATE archive_file SET issue_id = NULL WHERE id = '.$file['id']);
                continue;
            }

            $this->execute('UPDATE archive_file SET issue_id = '.$issue['id'].' WHERE id = '.$file['id']);
        }

        $table = $this->table('archive_file');

        $table->removeColumn('issue')
              ->removeColumn('publication') // Can get this from the issue record
              ->addForeignKey('issue_id', 'archive_issue', 'id')
              ->addIndex('issue_id')
              ->save();
    }

    public function down()
    {
        $this->dropTable('archive_file');

        $this->dropTable('archive_issue');

        $this->dropTable('archive_publication');
    }
}
