<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Poll Response class
 */

/**
 * @codeCoverageIgnore
 */
class PollResponse extends BaseDB
{
    public $dbtable = 'polls_response';

    public function __construct($id = null)
    {
        $fields = array(
            'poll' => new Type\ForeignKey('FelixOnline\Core\Poll'),
            'option' => new Type\ForeignKey('FelixOnline\Core\PollOption'),
            'ip' => new Type\TextField(),
            'useragent' => new Type\TextField(),
        );

        parent::__construct($fields, $id, null, true);
    }
}
