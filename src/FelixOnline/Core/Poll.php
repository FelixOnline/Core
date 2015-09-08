<?php
namespace FelixOnline\Core;
/*
 * Poll class
 */
class Poll extends BaseDB
{
	public $dbtable = 'polls';

	function __construct($id = NULL) {
		$fields = array(
			'author' => new Type\ForeignKey('FelixOnline\Core\User'),
			'question' => new Type\TextField(),
			'ended' => new Type\BooleanField(array(
				'null' => false,
			)),
			'location' => new Type\ForeignKey('FelixOnline\Core\PollLocation'),
			'hide_results' => new Type\BooleanField(array(
				'null' => false,
			)),
		);

		parent::__construct($fields, $id);
	}

	public function getArticles() {
		$polls = \FelixOnline\Core\BaseManager::build('FelixOnline\Core\ArticlePolls', 'article_polls');
		$polls->filter("poll = '%s'", array($this->getId()));

		$polls = $polls->values();
		return($polls);
	}

	public function canUserRespond() {
		if($this->getEnded()) {
			return false;
		}
		
		$ip = $_SERVER['REMOTE_ADDR'];
		$host = $_SERVER['HTTP_USER_AGENT'];

		$polls = \FelixOnline\Core\BaseManager::build('FelixOnline\Core\PollResponse', 'polls_response');
		$polls->filter("poll = '%s'", array($this->getId()))
			  ->filter("ip = '%s'", array($ip))
			  ->filter("useragent = '%s'", array($host));

		$polls = $polls->count();
		if($polls == 0) {
			return true;
		} else {
			return false;
		}
	}

	public function getResponses() {
		$answers = array();
		$total_count = 0;

		// get answers
		$option = \FelixOnline\Core\BaseManager::build('FelixOnline\Core\PollOption', 'polls_option');
		$option->filter("poll = '%s'", array($this->getId()));
		$option = $option->values();

		foreach($option as $option_inst) {

			$resp = \FelixOnline\Core\BaseManager::build('FelixOnline\Core\PollResponse', 'polls_response');
			$resp->filter("poll = '%s'", array($this->getId()))
				 ->filter("option = '%s'", array($option_inst->getId()));

			$resp = $resp->count();
			$answers[$option_inst->getId()] = array('id' => $option_inst->getId(), 'label' => $option_inst->getText(), 'count' => $resp);

			$total_count += $resp;
		}

		return array('total' => $total_count, 'answers' => $answers);
	}
}
