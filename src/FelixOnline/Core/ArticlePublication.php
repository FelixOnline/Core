<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Article - Publication History
 */

/**
 * @codeCoverageIgnore
 */
class ArticlePublication extends BaseDb
{
    public $dbtable = 'article_publication';

    public function __construct($id = null)
    {
        $fields = array(
            'article' => new Type\ForeignKey('FelixOnline\Core\Article'),
            'publication_date' => new Type\DateTimeField(),
            'published_by' => new Type\ForeignKey('FelixOnline\Core\User'),
            'republished' => new Type\BooleanField()
        );

        parent::__construct($fields, $id);
    }
}
