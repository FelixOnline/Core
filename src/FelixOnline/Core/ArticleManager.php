<?php
namespace FelixOnline\Core;
/**
 * Article manager
 */
class ArticleManager extends BaseManager
{
	protected $table = 'article';
	protected $class = 'Article';

	public function getMostPopular($number_to_get) {
		$sql = App::query(
			"SELECT
				DISTINCT article AS id,
				COUNT(article) AS c
			FROM (
				SELECT article FROM article_visit AS av
				INNER JOIN article AS a
				ON (av.article=a.id)
				WHERE a.published IS NOT NULL
				AND a.published > FROM_UNIXTIME(UNIX_TIMESTAMP() - 1814400)
				ORDER BY timestamp DESC LIMIT 500
			) AS t GROUP BY article ORDER BY c DESC LIMIT %i",
			array($number_to_get)
		);

		return App::$db->get_results($sql);
	}

	public static function getMostCommented($threshold, $number_to_get) {
		$sql = App::query(
			"SELECT
				article AS id,
				SUM(count) AS count
			FROM (
					(SELECT c.article,COUNT(*) AS count
					FROM `comment` AS c
					INNER JOIN `article` AS a ON (c.article=a.id)
					WHERE c.`active`=1
					AND timestamp>(DATE_SUB(NOW(),INTERVAL %i day))
					AND a.published<NOW()
					GROUP BY article
					ORDER BY timestamp DESC
					LIMIT 20)
				UNION ALL
					(SELECT ce.article,COUNT(*) AS count
					FROM `comment_ext` AS ce
					INNER JOIN `article` AS a ON (ce.article=a.id)
					WHERE ce.`active`=1
					AND pending=0
					AND timestamp>(DATE_SUB(NOW(),INTERVAL %i day))
					AND a.published<NOW()
					GROUP BY article
					ORDER BY timestamp DESC)
			) AS t
			GROUP BY article
			ORDER BY count DESC, article DESC LIMIT %i",
			array(
				$threshold,
				$threshold,
				$number_to_get
			)
		); // go for most recent comments instead
		return App::$db->get_results($sql);
	}
}
