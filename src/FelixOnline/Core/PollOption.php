<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Poll Option class
 */

/**
 * @codeCoverageIgnore
 */
class PollOption extends BaseDB
{
    public $dbtable = 'polls_option';

    public function __construct($id = null)
    {
        $fields = array(
            'poll' => new Type\ForeignKey('FelixOnline\Core\Poll'),
            'text' => new Type\TextField(),
        );

        parent::__construct($fields, $id);
    }
}
