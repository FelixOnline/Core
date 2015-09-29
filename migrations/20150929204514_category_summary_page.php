<?php

use Phinx\Migration\AbstractMigration;

class CategorySummaryPage extends AbstractMigration
{
    public function up()
    {
        $this->execute('INSERT INTO settings VALUES("articles_per_summary_section", "Number of articles to show per section on a parent category summary page", "4");');
    }

    public function down()
    {
        $this->execute('DELETE FROM settings WHERE setting = "articles_per_summary_section"');
    }
}
