<?php
namespace FelixOnline\Core;
/**
 * Article manager
 */
class ArticleManager extends BaseManager
{
	public $table = 'article';
	public $class = 'FelixOnline\Core\Article';

	public function getMostPopular($number_to_get)
	{
		$app = App::getInstance();

		$item = $app['cache']->getItem('articles/most_popular');
		$articles = $item->get(\Stash\Item::SP_PRECOMPUTE, 300);

		if ($item->isMiss()) {
			$sql = $app['safesql']->query(
				"SELECT
					DISTINCT article AS id,
					COUNT(article) AS c
				FROM (
					SELECT article FROM article_visit AS av
					INNER JOIN article AS a
					ON (av.article=a.id)
					WHERE a.published IS NOT NULL
					AND a.published >= NOW() - INTERVAL 3 WEEK
					ORDER BY timestamp DESC LIMIT 500
				) AS t GROUP BY article ORDER BY c DESC LIMIT %i",
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
					WHERE c.`active`=1
					AND c.`spam`=0
					AND timestamp >= NOW() - INTERVAL 3 WEEK
					AND a.published IS NOT NULL
					AND a.published < NOW()
					AND a.published >= NOW() - INTERVAL 3 WEEK
					GROUP BY article
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
