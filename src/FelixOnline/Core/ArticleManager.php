<?php
namespace FelixOnline\Core;
/**
 * Article manager
 */
class ArticleManager extends BaseManager
{
	public $table = 'article';
	public $class = 'FelixOnline\Core\Article';

	public function enablePublishedFilter()
	{
		$publishedManager = BaseManager::build('\FelixOnline\Core\ArticlePublication', 'article_publication');

		$publishedManager = $publishedManager->filter('republished = 0')
						 					 ->filter('publication_date <= NOW()');

		$this->join($publishedManager, 'LEFT', 'id', 'article');
		$this->order(array('article_publication.publication_date', 'id'), 'DESC');

		return $this;
	}

	public function getMostPopular($number_to_get)
	{
		$app = App::getInstance();

		$item = $app['cache']->getItem('articles/most_popular');
		$articles = $item->get(\Stash\Item::SP_PRECOMPUTE, 300);

		if ($item->isMiss()) {
			$sql = $app['safesql']->query(
				"SELECT
						DISTINCT av_id AS id,
						COUNT(av_id) AS c
					FROM (
						SELECT av.article AS av_id FROM article_visit AS av
						INNER JOIN article AS a
							ON (av.article=a.id)
						INNER JOIN article_publication AS ap
							ON a.id = ap.article
							AND ap.republished = 0
					        AND ap.publication_date >= NOW() - INTERVAL 3 WEEK
						ORDER BY timestamp DESC LIMIT 500
					) AS t GROUP BY id ORDER BY c DESC LIMIT %i",
				array($number_to_get)
			);
			$results = $this->query($sql);

			if (!is_null($results)) {
				$articles = $this->resultToModels($results);
			} else {
				$articles = null;
			}

			$item->set($articles, 1800); // expire in 30 mins
		}

		return $articles;
	}

	public function getMostCommented($number_to_get)
	{
		$app = App::getInstance();

		$item = $app['cache']->getItem('articles/most_commented');
		$articles = $item->get(\Stash\Item::SP_PRECOMPUTE, 300);

		if ($item->isMiss()) {
			$sql = $app['safesql']->query(
				"SELECT
					article AS id,
					SUM(count) AS count
				FROM (
					SELECT c.article,COUNT(*) AS count
					FROM `comment` AS c
					INNER JOIN `article` AS a ON (c.article=a.id)
					INNER JOIN article_publication
						ON a.id = article_publication.article
						AND article_publication.republished = 0
						AND article_publication.publication_date >= NOW() - INTERVAL 3 WEEK
					WHERE c.`active`=1
					AND c.`spam`=0
					AND c.`pending`=0
					AND timestamp >= NOW() - INTERVAL 3 WEEK
					GROUP BY c.article
					ORDER BY count DESC
					LIMIT 20
				) AS t
				GROUP BY article
				ORDER BY count DESC LIMIT %i",
				array(
					$number_to_get
				)
			); // go for most recent comments instead

			$results = $this->query($sql);

			if (!is_null($results)) {
				$articles = $this->resultToModels($results);
			} else {
				$articles = null;
			}

			$item->set($articles, 1800); // expire in 30 mins
		}

		return $articles;
	}
}
