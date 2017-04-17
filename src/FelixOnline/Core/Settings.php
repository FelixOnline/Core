<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Settings
 */
class Settings extends BaseDB
{
    public $dbtable = 'settings';

    public function __construct($key = null)
    {
        $fields = array(
            'setting' => new Type\CharField(array('primary' => true)),
            'description' => new Type\CharField(),
            'value' => new Type\CharField()
        );

        parent::__construct($fields, $key);
    }

    public static function get($key)
    {
        $setting = new static($key);

        return $setting->getValue();
    }
}
