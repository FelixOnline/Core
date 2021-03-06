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
 * Live blog instances registered with the Sprinkler liveblog system.
 *
 * Fields:
 * - sprinkler_prefix: (CharField) Prefix defined on Sprinkler system (user agents connect to Sprinkler via this prefix to access this blog's posts).
 * - id: (IntegerField) this primary key is automatically added by the database layer.
 * - deleted: (BooleanField) this indicates if the database layer should not load this record (soft deletion) and this field is automatically added by the database layer.
 *
 * @author Philip Kent <philip.kent@me.com>
 * @license BSD
 * @codeCoverageIgnore
 */
class Blog extends BaseDB
{
    /**
     * @var char $dbtable Table to reference.
     */
    public $dbtable = 'blogs';

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
            'sprinkler_prefix' => new Type\CharField(),
        );

        parent::__construct($fields, $id, null, false);
    }
}
