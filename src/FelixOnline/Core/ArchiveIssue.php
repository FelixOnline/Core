<?php
namespace FelixOnline\Core;

use FelixOnline\Base\BaseDB;
use FelixOnline\Base\BaseManager;
use FelixOnline\Base\Type;
use FelixOnline\Base\App;
use FelixOnline\Exceptions\InternalException;

/*
 * Issue Archive - Issue
 */

class ArchiveIssue extends BaseDb
{
    public $dbtable = 'archive_issue';

    private $relevance = false;

    public function __construct($id = null)
    {
        $fields = array(
            'issue' => new Type\IntegerField(),
            'date' => new Type\DateTimeField(),
            'publication' => new Type\ForeignKey('FelixOnline\Core\ArchivePublication'),
            'inactive' => new Type\BooleanField(),
        );

        parent::__construct($fields, $id);
    }

    public function getURL()
    {
        $url = STANDARD_URL.'issuearchive/issue/'.$this->getId();
        return $url;
    }

    public function getThumbnailURL()
    {
        $url = $this->getPrimaryFile()->getThumbnailURL();
        return $url;
    }

    public function getDownloadURL()
    {
        $url = $this->getURL().'/download';
        return $url;
    }

    public function getPrimaryFile()
    {
        $manager = BaseManager::build('FelixOnline\Core\ArchiveFile', 'archive_file');

        try {
            $file = $manager->filter('issue_id = %i', array($this->getId()))->filter('part = "A"')->one();

            return $file;
        } catch (\Exception $e) {
            // Find first part that DOES exist

            $manager = BaseManager::build('FelixOnline\Core\ArchiveFile', 'archive_file');
            $file = $manager->filter('issue_id = %i', array($this->getId()))
                            ->order("part", "ASC")
                            ->limit(0, 1)
                            ->values();

            return $file[0];
        }
    }

    public function getFiles()
    {
        $manager = BaseManager::build('FelixOnline\Core\ArchiveFile', 'archive_file');

        return $manager->filter('issue_id = %i', array($this->getId()))->values();
    }

    public function getSpecificFile($part)
    {
        $manager = BaseManager::build('FelixOnline\Core\ArchiveFile', 'archive_file');

        return $manager->filter('issue_id = %i', array($this->getId()))->filter('part = "%s"', array($part))->one();
    }

    // Relevance - used in the search feature
    public function hasRelevance()
    {
        if ($this->relevance === false) {
            return false;
        }

        return true;
    }

    public function getRelevance()
    {
        return $this->relevance;
    }

    public function setRelevance($relevance)
    {
        $this->relevance = $relevance;

        return $this;
    }
}
