<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Article Topic class
 */

/**
 * @codeCoverageIgnore
 */
class ArticleTopic extends BaseDB
{
    public $dbtable = 'article_topic';

    public function __construct($id = null)
    {
        $fields = array(
            'topic' => new Type\ForeignKey('FelixOnline\Core\Topic'),
            'article' => new Type\ForeignKey('FelixOnline\Core\Article'),
        );

        parent::__construct($fields, $id);
    }
}
