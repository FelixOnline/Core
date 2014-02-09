<?php

use Phinx\Migration\AbstractMigration;

class CommentExtMove extends AbstractMigration
{
    /**
     * Migrate Up.
     */
    public function up()
    {
		// Allow null user
		$this->execute('ALTER TABLE comment MODIFY user VARCHAR(16)');

		// Remove all comments on the zero article
		$this->execute('DELETE FROM comment_ext WHERE article = 0');
		
		// Move all external comments to the comment table
		$this->execute('
			INSERT INTO comment (
				id,
				article,
				external,
				name,
				comment,
				timestamp,
				ip,
				referer,
				useragent,
				email,
				active,
				spam,
				pending,
				reply,
				likes,
				dislikes
			)
			SELECT
				id,
				article,
				1,
				name,
				comment,
				timestamp,
				IP,
				referer,
				useragent,
				email,
				active,
				spam,
				pending,
				reply,
				likes,
				dislikes
			FROM comment_ext
		');

		$this->execute('DROP TABLE comment_ext');
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
		// There is no going back from this
    }
}
