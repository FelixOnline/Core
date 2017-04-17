<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Article comment status (for admin pages)
 */

/**
 * @codeCoverageIgnore
 */
class ArticleCommentStatus extends BaseDb
{
    public $dbtable = 'article_comment_status';

    const ARTICLE_COMMENTS_OFF = 0;
    const ARTICLE_COMMENTS_ON = 1;
    const ARTICLE_COMMENTS_INTERNAL = 2;

    public function __construct($id = null)
    {
        $fields = array(
            'description' => new Type\CharField()
        );

        parent::__construct($fields, $id);
    }
}
