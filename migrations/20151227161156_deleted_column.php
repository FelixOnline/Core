<?php

use Phinx\Migration\AbstractMigration;

class DeletedColumn extends AbstractMigration
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
        $tables = array("advert", "advert_category", "akismet_log", "archive_file", "archive_issue", "archive_publication",
            "article", "article_author", "article_comment_status", "article_polls", "article_topic", "article_visit",
            "blogs", "blog_post", "category", "category_author", "comment", "comment_like", "cookies", "email_validation",
            "frontpage", "image", "link", "login", "notices", "pages", "polls", "polls_location", "polls_option", "polls_response",
            "roles", "settings", "text_story", "topic", "user", "user_roles");

        foreach($tables as $table) {
            echo $table."\n";

            $table = $this->table($table);

            $table->addColumn('deleted', 'boolean', array('default' => 0))->update();
        }
    }
}
