<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Frontpage class
 * Represents the frontpage
 *
 * Fields:
 */

/**
 * @codeCoverageIgnore
 */
class Frontpage extends BaseDb
{
    public $dbtable = 'frontpage';

    /**
     * Constructor
     *
     * @param integer $id = Frontpage slot record number
     */
    public function __construct($id = null)
    {
        $fields = array(
            'article' => new Type\ForeignKey('FelixOnline\Core\Article'),
            'section' => new Type\CharField(),
            'sort_order' => new Type\IntegerField(),
        );

        parent::__construct($fields, $id);
    }
}
