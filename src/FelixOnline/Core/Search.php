<?php
namespace FelixOnline\Core;
/**
 * Search query
 */

// FIXME Move this into separate models
class Search
{
	protected $query;

	function __construct($query) {
		$app = App::getInstance();
		$this->db = $app['db'];
		$this->safesql = $app['safesql'];
		$this->pageSize = Settings::get('articles_per_search_page');

		$this->query = $query;
	}

	public function people() {
		$people = array();

		$sql = $this->safesql->query("SELECT
			user, name
			FROM user
			WHERE name LIKE '%s'
			AND deleted = 0
			ORDER BY name ASC",
			array(
				"%" . $this->query. "%"
			)
		);
		$results = $this->db->get_results($sql);
		
		if (!is_null($results)) {
			foreach($results as $person) {
				array_push($people, array(
					'name' => $person->name,
					'user' => $person->user
				));
			}
		}
		
		return array(
			'count' => count($people),
			'people' => $people
		);
	}

	public function articleTitles($page = 1) {
		$filters = "FROM article
			WHERE title LIKE '%s'
			AND hidden = 0
			AND deleted = 0
			AND published < NOW()
			ORDER BY article.date DESC,
			article.id DESC";

		// get count
		$sql = $this->safesql->query(
			"SELECT
				COUNT(id)
			" . $filters,
			array(
				'%'.$this->query.'%',
			)
		);
		$count = (int)$this->db->get_var($sql);

		if ($count == 0) {
			return array(
				'count' => 0,
				'articles' => array()
			);
		}

		$sql = $this->safesql->query(
			"SELECT
				id
			" . $filters . "
			LIMIT %i, %i",
			array(
				'%'.$this->query.'%',
				($page - 1) * $this->pageSize,
				$this->pageSize
			)
		);
		$results = $this->db->get_results($sql);

		if (is_null($results)) {
			throw new InternalException("Results array is null");
		} else {
			$articles = array();
			foreach ($results as $a) {
				array_push($articles, new Article($a->id));
			}
			return array(
				'count' => $count,
				'articles' => $articles
			);
		}
	}

	public function articleContent($page = 1) {
		$filters = "FROM `article`
			INNER JOIN `text_story`
			ON (article.text1 = text_story.id)
			WHERE text_story.content LIKE '%s'
			AND article.hidden = 0
			AND article.deleted = 0
			AND text_story.deleted = 0
			AND article.published < NOW()
			ORDER BY article.date DESC,
			article.id DESC";

		// get count

		$sql = $this->safesql->query(
			"SELECT
				COUNT(article.id)
			" . $filters,
			array(
				'%'.$this->query.'%',
			)
		);

		die($sql);

		$count = (int)$this->db->get_var($sql);

		if ($count == 0) {
			return array(
				'count' => 0,
				'articles' => array()
			);
		}
	
		$sql = $this->safesql->query(
			"SELECT
				article.id
			" . $filters . "
			LIMIT %i, %i",
			array(
				'%'.$this->query.'%',
				($page - 1) * $this->pageSize,
				$this->pageSize
			)
		);
		$results = $this->db->get_results($sql);

		if (is_null($results)) {
			throw new InternalException("Results array is null");
		} else {
			$articles = array();
			foreach ($results as $a) {
				array_push($articles, new Article($a->id));
			}
			return array(
				'count' => $count,
				'articles' => $articles
			);
		}
	}
}
