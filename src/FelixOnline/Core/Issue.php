<?php
namespace FelixOnline\Core;
/*
 * Issue Class
 *
 * Fields:
 *      id          - Issue id
 *      PubDate     - Date of publish (YYYY-MM-DD)
 *      IssueNo     - Issue number
 *      PubNo       - Publication number (references publication table)
 *      Description - Text description of publication
 *      Year        - Temporary
 */
class Issue {
	private $id;
	private $issueno;
	private $pubno;
	private $description;
	private $year;
	private $pubdate;
	private $relevance;

	private $fields = array();

	function __construct($id = NULL) {
		global $dba;
		$this->dba = $dba;
		$this->safesql = new \SafeSQL_MySQLi($dba->dbh);
		if($id !== NULL) {
			$sql = $this->safesql->query("SELECT
						`id`,
						`PubDate`,
						`IssueNo`,
						`PubNo`,
						`Description`,
						`Year`
					FROM `Issues`
					WHERE id=%i", array($id));

			$row = $this->dba->get_row($sql);

			if(!$row) {
				throw new \FelixOnline\Exceptions\ModelNotFoundException('No issue', $this, $id);
			}

			$this->id = $id;
			$this->issueno = $row->IssueNo;
			$this->pubno = $row->PubNo;
			$this->description = $row->Description;
			$this->year = $row->Year;
			$this->pubdate = $row->PubDate;

			return $this;
		} else {
			throw new \FelixOnline\Exceptions\InternalException('Issue must be specified');
		}
	}

	public function getId() {
		return $this->id;
	}

	public function getIssueNo() {
		return $this->issueno;
	}

	public function getPubNo() {
		return $this->pubno;
	}

	public function getPubDate() {
		return $this->pubdate;
	}

	public function hasRelevance() {
		return $this->relevance;
	}

	public function getRelevance() {
		return $this->hasRelevance();
	}

	public function setRelevance($r) {
		$this->relevance = $r;
	}

	/*
	 * Public: Get URL
	 *
	 * Returns string
	 */
	public function getURL() {
		$url = STANDARD_URL.'issuearchive/issue/'.$this->getId();
		return $url;
	}

	/*
	 * Public: Get download URL
	 *
	 * Returns string
	 */
	public function getDownloadURL() {
		$url = $this->getURL().'/download';
		return $url;
	}

	/*
	 * Public: Get thumbnail
	 * Gets thumbnail filename
	 *
	 * TODO: clean up
	 *
	 * Returns string
	 */
	public function getThumbnail() {
		$thumb = substr($this->getFile(),8,(strlen($this->getFile())-11)).'png';
		return $thumb;
	}

	/*
	 * Public: Get thumbnail url
	 *
	 * Returns string
	 */
	public function getThumbnailURL() {
		$url = 'http://felixonline.co.uk/archive/thumbs/'.$this->getThumbnail();
		return $url;
	}

	/**
	 * Public: Get file
	 *
	 * Returns string
	 */
	public function getFile() {
		if (!array_key_exists('file', $this->fields)) {
			$sql = $this->safesql->query(
					"SELECT
						FileName
					FROM Files
					WHERE PubNo = %i
					AND IssueNo = %i",
					array(
						$this->getPubNo(),
						$this->getIssueNo()
					));
			$result = $this->dba->get_row($sql);
			$this->fields['file'] = $result->FileName;
		}
		return $this->fields['file'];
	}

	/**
	 * Public: Get file name
	 *
	 * Returns string
	 */
	public function getFileName() {
		if (!array_key_exists('file_name', $this->fields)) {
			$file = $this->getFile();
			preg_match('/\/(\w+)_[A-Z]/', $file, $matches);
			$filename = $matches[1] . '.pdf';
			$this->fields['file_name'] = $file_name;
		}
		return $this->fields['file_name'];
	}

	public function getOutput() {
		return array(
			'id' => $this->id,
			'pub_date' => $this->pubdate,
			'issue_no' => $this->issueno,
			'pub_no' => $this->pubno,
			'description' => $this->description,
			'year' => $this->year);
	}
}

