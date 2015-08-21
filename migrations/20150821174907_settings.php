<?php

use Phinx\Migration\AbstractMigration;

class Settings extends AbstractMigration
{
    public function up()
    {
        $table = $this->table('settings', array('id' => false, 'primary_key' => array('setting')));
        $table->addColumn('setting', 'string', array('limit' => 100))
              ->addColumn('description', 'string', array('limit' => 250))
              ->addColumn('value', 'string', array('limit' => 300))
              ->save();

        $this->execute('INSERT INTO settings VALUES("articles_per_search_page", "Number of articles shown on each page of a search for articles", "8");');
        $this->execute('INSERT INTO settings VALUES("image_url", "URL including trailing slash to host pointing to the main site\'s image directory (must point to the Felix image auto-resizer)", "http://img.felixonline.co.uk/");');
        $this->execute('INSERT INTO settings VALUES("email_extcomment_notifyaddr", "Comma-separated list of email addresses to notify when a comment requires moderation/abuse reviewing", "felix@imperial.ac.uk");');
        $this->execute('INSERT INTO settings VALUES("email_replyto_addr", "Reply-to email for emails sent by the system", "no-reply@imperial.ac.uk");');
        $this->execute('INSERT INTO settings VALUES("email_replyto_name", "Reply-to name for emails sent by the system", "Felix Online");');
        $this->execute('INSERT INTO settings VALUES("default_img_uri", "Filename (in the img directory of the main site) for the default image", "defaultimage.jpg");');
        $this->execute('INSERT INTO settings VALUES("current_theme", "Folder name for the active theme on the main site", "2014");');
        $this->execute('INSERT INTO settings VALUES("popular_articles", "Number of articles to show in the most read/most commented widgets", "5");');
        $this->execute('INSERT INTO settings VALUES("articles_per_cat_page", "Number of articles to show in the first page of a category view", "8");');
        $this->execute('INSERT INTO settings VALUES("articles_per_second_cat_page", "Number of articles to show on all pages of a category view after the first", "10");');
        $this->execute('INSERT INTO settings VALUES("articles_per_user_page", "Number of articles to show per page on a user view", "8");');
        $this->execute('INSERT INTO settings VALUES("number_of_popular_articles_user", "Number of articles to show in a user\'s most popular articles widget", "5");');
        $this->execute('INSERT INTO settings VALUES("number_of_pages_in_page_list", "Maximum number of pages to show in a paginator before ellipses are shown", "14");');
        $this->execute('INSERT INTO settings VALUES("news_category_id", "(For the front page) ID number of category: News", "1");');
        $this->execute('INSERT INTO settings VALUES("comment_category_id", "(For the front page) ID number of category: Comment", "2");');
        $this->execute('INSERT INTO settings VALUES("sport_category_id", "(For the front page) ID number of category: Sports", "18");');
        $this->execute('INSERT INTO settings VALUES("cands_category_id", "(For the front page) ID number of category: Clubs and Societies", "23");');
        $this->execute('INSERT INTO settings VALUES("rss_img", "Filename (in the image directory of the main site) to show in RSS feeds", "defaultimage.jpg");');
        $this->execute('INSERT INTO settings VALUES("rss_name", "Name for RSS feeds (may be preceeded by the category/user name)", "Felix Online");');
        $this->execute('INSERT INTO settings VALUES("rss_description", "Description for RSS feeds (may be preceeded by the category/user name)", "Latest articles from Felix Online");');
        $this->execute('INSERT INTO settings VALUES("rss_copyright", "Copyright statement for RSS feeds", "(c) Felix Online");');
        $this->execute('INSERT INTO settings VALUES("rss_articles", "Maximum number of articles to show in a RSS feed", "30");');
    }

    public function down()
    {
        $this->dropTable('settings');
    }
}
