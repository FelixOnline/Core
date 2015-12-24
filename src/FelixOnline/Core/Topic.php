<?php
namespace FelixOnline\Core;
/*
 * Topic class
 */
class Topic extends BaseDB
{
	public $dbtable = 'topic';

	/**
	 * Constructor for Text class
	 *
	 * $id - ID of text (optional)
	 */
	function __construct ($id = NULL) {
		$fields = array(
			'slug' => new Type\CharField(array('primary' => true)),
			'name' => new Type\CharField(),
			'text' => new Type\TextField(),
			'disabled' => new Type\BooleanField(),
			'image' => new Type\ForeignKey('FelixOnline\Core\Image')
		);

		parent::__construct($fields, $id);
	}

	/**
	 * Get date that the first article was posted
	 */
	function getStartDate() {
		$topic = BaseManager::build('FelixOnline\Core\Topic', 'article_topic', 'topic')
			->filter('topic = "%s"', array($this->getSlug()));

		$article = BaseManager::build('FelixOnline\Core\Article', 'article', 'id')
			->join($topic, 'LEFT', 'id', 'article')
			->filter('published > 0')
			->order('published', 'ASC')
			->limit(0, 1)
			->values();

		return($article[0]->getPublished());
	}

	/**
	 * Get date that the last article was posted
	 */
	function getEndDate() {
		$topic = BaseManager::build('FelixOnline\Core\Topic', 'article_topic', 'topic')
			->filter('topic = "%s"', array($this->getSlug()));

		$article = BaseManager::build('FelixOnline\Core\Article', 'article', 'id')
			->join($topic, 'LEFT', 'id', 'article')
			->filter('published > 0')
			->order('published', 'DESC')
			->limit(0, 1)
			->values();

		return($article[0]->getPublished());
	}

	public function getURL($i = 1) {
		$app = App::getInstance();

		if($i != 1) {
			$page = '/'.$i;
		} else {
			$page = '';
		}

		return $app->getOption('base_url') . 'topic/' . $this->getSlug() . $page;
	}
}
