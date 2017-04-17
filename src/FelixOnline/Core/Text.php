<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Text class
 */

/**
 * @codeCoverageIgnore
 */
class Text extends BaseDB
{
    public $dbtable = 'text_story';

    /**
     * Constructor for Text class
     *
     * $id - ID of text (optional)
     */
    public function __construct($id = null)
    {
        $fields = array(
            'user' => new Type\ForeignKey('FelixOnline\Core\User'),
            'content' => new Type\TextField(),
            'timestamp' => new Type\DateTimeField(),
            'converted' => new Type\BooleanField(),
        );

        parent::__construct($fields, $id);
    }
}
