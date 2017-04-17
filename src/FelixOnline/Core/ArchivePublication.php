<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Issue Archive - Publication
 */

/**
 * @codeCoverageIgnore
 */
class ArchivePublication extends BaseDb
{
    public $dbtable = 'archive_publication';

    public function __construct($id = null)
    {
        $fields = array(
            'name' => new Type\CharField(),
            'inactive' => new Type\BooleanField(),
        );

        parent::__construct($fields, $id);
    }
}
