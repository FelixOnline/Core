<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Blog post class
 *
 * Fields:
 *	  id:		 - id of blog post
 *	  blog:	   - id of blog that post is on
 *	  content:	- content of post
 *	  timestamp:  - timestamp of post (updates on modification)
 *	  author:	 - uname of post author
 *	  type:	   - type of post [optional]
 *	  meta:	   - JSON encoded array of post meta [optional]
 *	  visible:	-
 */

/**
 * @codeCoverageIgnore
 */
class BlogPost extends BaseDb
{
    public $dbtable = 'blog_post';

    public function __construct($id = null)
    {
        $fields = array(
            'blog' => new Type\ForeignKey('FelixOnline\Core\Blog'),
            'content' => new Type\TextField(),
            'timestamp' => new Type\DateTimeField(),
            'author' => new Type\ForeignKey('FelixOnline\Core\User'),
            'breaking' => new Type\BooleanField(array('null' => false)),
            'title' => new Type\CharField()
        );

        parent::__construct($fields, $id);
    }
}
