<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Notice class
 */
class Notice extends BaseDB
{
    public $dbtable = 'notices';

    public function __construct($id = null)
    {
        $fields = array(
            'author' => new Type\ForeignKey('FelixOnline\Core\User'),
            'content' => new Type\TextField(),
            'start_time' => new Type\DateTimeField(),
            'end_time' => new Type\DateTimeField(),
            'hidden' => new Type\BooleanField(array(
                'null' => false,
            )),
            'frontpage' => new Type\BooleanField(array(
                'null' => false,
            )),
            'sort_order' => new Type\IntegerField(),
        );

        parent::__construct($fields, $id);
    }

    public function isEnded()
    {
        if ($this->getHidden() == true || $this->getStartDate() < NOW() || $this->getEndDate() > NOW()) {
            return true;
        }

        return false;
    }

    /**
     * Private: Clean text
     */
    private function cleanText($text)
    {
        $result = strip_tags($text, '<b><i><u><img><a><strong><em>'); // Gets rid of html tags except for a few
        $result = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $result); // Remove style attributes
        return $result;
    }
}
