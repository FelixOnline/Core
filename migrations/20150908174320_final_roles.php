<?php

use Phinx\Migration\AbstractMigration;

class FinalRoles extends AbstractMigration
{
    public function up()
    {
        $this->execute("INSERT INTO `roles` (`id`, `name`, `description`, `parent`) VALUES
                            (2, 'archivist', 'Issue archive manager', 1),
                            (5, 'sysAdmin', 'System settings administrator', 1),
                            (6, 'webMaster', 'Website content manager', 1),
                            (7, 'seniorEditor', 'Editor for all sections', 6),
                            (8, 'sectionEditor', 'Editor for specific sections', 7),
                            (9, 'author', 'Article author', 8),
                            (10, 'commentEditor', 'Comment editor', 6),
                            (11, 'commentModerator', 'Comment moderator (pending comments only)', 10),
                            (12, 'adverts', 'Advert manager', 6);");
    }

    public function down()
    {
        $this->execute("DELETE FROM `roles` WHERE id = 2");
        $this->execute("DELETE FROM `roles` WHERE id > 4 AND id < 13");
    }
}
