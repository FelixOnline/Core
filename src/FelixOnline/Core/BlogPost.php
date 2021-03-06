<?php
namespace FelixOnline\Core;

// THIS FILE IS AUTOMATICALLY GENERATED
// To modify, use the Model Builder located in the "build" directory.

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/**
 * Live blog posts.
 *
 * Fields:
 * - blog: (ForeignKey) ID number of blog.
 * - title: (CharField) Title for blog post.
 * - content: (TextField) Liveblog post contents.
 * - timestamp: (DateTimeField) Date and time the post was published.
 * - author: (CharField) Username of individual who authored and published the blog post.
 * - sprinkler_prefix: (ForeignKey) Prefix defined on Sprinkler system (user agents connect to Sprinkler via this prefix to access this blog's posts).
 * - breaking: (BooleanField) Flags the blog post as containing breaking news.
 * - id: (IntegerField) this primary key is automatically added by the database layer.
 * - deleted: (BooleanField) this indicates if the database layer should not load this record (soft deletion) and this field is automatically added by the database layer.
 *
 * @author Philip Kent <philip.kent@me.com>
 * @license BSD
 * @codeCoverageIgnore
 */
class BlogPost extends BaseDB
{
    /**
     * @var char $dbtable Table to reference.
     */
    public $dbtable = 'blog_post';

    /**
     * Constructor: prepares a new instance of this model.
     *
     * @param int $id If specified, fetch record with this primary key from the database. If not specified, a new empty record is generated.
     *
     * @throws FelixOnline\Exceptions\InternalException if definition is incorrectly configured.
     * @throws FelixOnline\Exceptions\ModelNotFoundException if primary key specified and the associated record has been deleted or is not present.
     */
    public function __construct($id = null)
    {
        $fields = array(
            'blog' => new Type\ForeignKey('FelixOnline\Core\Blog'),
            'title' => new Type\CharField(),
            'content' => new Type\TextField(),
            'timestamp' => new Type\DateTimeField(),
            'author' => new Type\CharField(),
            'sprinkler_prefix' => new Type\ForeignKey('FelixOnline\Core\User'),
            'breaking' => new Type\BooleanField(array(
                "null" => false,
            )),
        );

        parent::__construct($fields, $id, null, false);
    }
}
