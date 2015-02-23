<?php
namespace FelixOnline\Core;
/*
 * Notice class
 */
class Notice extends BaseDB
{
	public $dbtable = 'notices';

	function __construct($id = NULL) {
		$fields = array(
			'author' => new Type\ForeignKey('User'),
			'text' => new Type\ForeignKey('FelixOnline\Core\Text'),
			'start_date' => new Type\DateTimeField(),
			'end_date' => new Type\DateTimeField(),
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

	public function isEnded() {
		if($this->getHidden() == TRUE || $this->getStartDate() < NOW() || $this->getEndDate() > NOW()) {
			return true;
		}

		return false;
	}

	/**
	 * Private: Clean text
	 */
	private function cleanText($text) {
		$result = strip_tags($text, '<b><i><u><img><a><strong><em>'); // Gets rid of html tags except for a few
		$result = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $result); // Remove style attributes
		return $result;
	}

	/**
	 * Public: Get article content
	 */
	public function getContent() {
		$string = $this->getText()->getContent();

		$string = $this->cleanText($string);

		return $string;
	}
}
