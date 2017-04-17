<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Blog Class
 *
 * Fields:
 *	  id:		 - id of page
 *	  name:	   - name of blog
 *	  slug:	   - url slug of page
 *	  controller: - name of controller used to handle blog
 *	  sticky:	 -
 */

/**
 * @codeCoverageIgnore
 */
class Blog extends BaseDb
{
    public $dbtable = 'blogs';

    public function __construct($id = null)
    {
        $fields = array(
            'sprinkler_prefix' => new Type\CharField()
        );

        parent::__construct($fields, $id);
    }
}
