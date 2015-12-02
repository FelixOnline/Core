<?php
namespace FelixOnline\Core;
/*
 * Issue Archive - File
 */

class ArchiveFile extends BaseDb {
	public $dbtable = 'archive_file';

	function __construct($id = NULL)
	{
		$fields = array(
			'issue_id' => new Type\ForeignKey('FelixOnline\Core\ArchiveIssue'),
			'part' => new Type\CharField(),
			'filename' => new Type\CharField(),
			'content' => new Type\TextField()
		);

		parent::__construct($fields, $id);
	}

	public function getDownloadURL() {
		$url = $this->getIssueId()->getId().'/download/'.$this->getPart();
		return $url;
	}

	public function getThumbnail() {
		$thumb = substr($this->getFilename(),8,(strlen($this->getFilename())-11)).'png';
		return $thumb;
	}

	public function getThumbnailURL() {
		$folder = \FelixOnline\Core\Settings::get('archive_url_location');
		$url = $folder.'/thumbs/'.$this->getThumbnail();
		return $url;
	}

	public function getOnlyFilename() {
		$file = $this->getFilename();
		preg_match('/\/(\w+)_[A-Z]/', $file, $matches);
		$filename = $matches[1] . '_' . $this->getPart() . '.pdf';

		return $filename;
	}
}
