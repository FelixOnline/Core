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
 * Table to record the response from Akismet upon submitting a comment as spam/ham.
 *
 * Fields:
 * - comment_id: (ForeignKey) ID number of comment.
 * - timestamp: (DateTimeField) Date and time that the Akismet request was made.
 * - action: (CharField) Action sent to Akismet (almost always "check").
 * - is_spam: (BooleanField) Akismet response as to whether the comment is spam or not.
 * - error: (TextField) Any error response from Akismet.
 * - id: (IntegerField) this primary key is automatically added by the database layer.
 * - deleted: (BooleanField) this indicates if the database layer should not load this record (soft deletion) and this field is automatically added by the database layer.
 *
 * @author Philip Kent <philip.kent@me.com>
 * @license BSD
 * @codeCoverageIgnore
 */
class AkismetLog extends BaseDB
{
    /**
     * @var char $dbtable Table to reference.
     */
    public $dbtable = 'akismet_log';

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
            'comment_id' => new Type\ForeignKey('FelixOnline\Core\Comment'),
            'timestamp' => new Type\DateTimeField(),
            'action' => new Type\CharField(),
            'is_spam' => new Type\BooleanField(),
            'error' => new Type\TextField(),
        );

        parent::__construct($fields, $id, null, true);
    }
}
