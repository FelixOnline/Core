<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Issue Archive - File
 */

class ArchiveFile extends BaseDb
{
    public $dbtable = 'archive_file';

    public function __construct($id = null)
    {
        $fields = array(
            'issue_id' => new Type\ForeignKey('FelixOnline\Core\ArchiveIssue'),
            'part' => new Type\CharField(),
            'filename' => new Type\CharField(),
            'content' => new Type\TextField()
        );

        parent::__construct($fields, $id);
    }

    public function getDownloadURL()
    {
        $url = $this->getIssueId()->getId().'/download/'.$this->getPart();
        return $url;
    }

    public function getThumbnail()
    {
        $thumb = substr($this->getFilename(), 8, (strlen($this->getFilename())-11)).'png';
        return $thumb;
    }

    public function getThumbnailURL()
    {
        $folder = Settings::get('archive_url_location');
        $url = $folder.'/thumbs/'.$this->getThumbnail();
        return $url;
    }

    public function getOnlyFilename()
    {
        $file = $this->getFilename();
        preg_match('/\/(\w+)_[A-Z]/', $file, $matches);
        $filename = $matches[1] . '_' . $this->getPart() . '.pdf';

        return $filename;
    }
}
