<?php

use Phinx\Migration\AbstractMigration;

class CategoryAllowNull extends AbstractMigration
{
	protected $articles = array(
		'top_slider_1',
		'top_slider_2',
		'top_slider_3',
		'top_slider_4',
		'top_sidebar_1',
		'top_sidebar_2',
		'top_sidebar_3',
		'top_sidebar_4',
		'top_sidebar_5',
	);

    /**
     * Migrate Up.
     */
    public function up()
    {
		foreach($this->articles as $article) {
			$this->execute('ALTER TABLE category MODIFY ' . $article . ' INT(11)');
		}

		// Check if any of the category articles are 0 and set to NULL
		$categories = $this->query('SELECT * FROM category');

		foreach ($categories as $category) {
			foreach ($this->articles as $article) {
				if ($category[$article] == 0) {
					$this->execute('UPDATE category SET ' . $article . ' = NULL WHERE id = ' . $category['id']);
				}
			}
		}
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		// Check if any of the category articles are NULL and set to 0
		$categories = $this->query('SELECT * FROM category');

		foreach ($categories as $category) {
			foreach ($this->articles as $article) {
				if (is_null($category[$article])) {
					$this->execute('UPDATE category SET ' . $article . ' = 0 WHERE id = ' . $category['id']);
				}
			}
		}

		foreach($this->articles as $article) {
			$this->execute('ALTER TABLE category MODIFY ' . $article . ' INT(11) NOT NULL');
		}
    }
}
