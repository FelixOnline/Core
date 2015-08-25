<?php

require_once __DIR__ . '/../../AppTestCase.php';
require_once __DIR__ . '/../../utilities.php';

class ArticleTest extends AppTestCase
{
	public $fixtures = array(
		'articles',
		'categories',
		'text_stories',
		'users',
		'article_authors',
		'comments',
		'images',
		'article_visits',
		'logins',
	);

	public function testGetTitle()
	{
		$article = new \FelixOnline\Core\Article(1);
		$this->assertEquals($article->getTitle(), 'Fighting for Libel Reform');
	}

	public function testGetURL()
	{
		$article = new \FelixOnline\Core\Article(1);
		$this->assertEquals($article->getURL(), 'http://localhost/news/1/fighting-for-libel-reform/');
	}

	public function testGetContent()
	{
		$article = new \FelixOnline\Core\Article(1);
		$this->assertEquals(
			$article->getContent(),
			'<p>As Imperial alumnus Simon Singh prepares to return to College to give a guest lecture on the libel laws in science of which he has fallen foul of, the Government have announced the commencement of a working group on libel reform.</p>'
		);
	}

	public function testGetTeaserFull()
	{
		$article = new \FelixOnline\Core\Article(1);
		$this->assertEquals(
			$article->getTeaserFull(),
			"In light of Simon Singh returning to Imperial to give a lecture on libel laws that he is personally embroiled in!"
		);
	}

	public function testGetTeaserFromContent()
	{
		$article = new \FelixOnline\Core\Article(2);
		$this->assertEquals(
			$article->getTeaserFull(),
			"All I bloody hear is the clock ticking. We all know what that sounds like, and we all know what the stabbing sounds of each second, &lsquo;tick&rsquo; or &lsquo;tock&rsquo;, means. Time is slipping..."
		);
	}

	public function testGetPreview()
	{
		$article = new \FelixOnline\Core\Article(1);
		$this->assertEquals(
			$article->getPreview(),
			"As Imperial alumnus Simon Singh prepares to return to College to give a guest lecture on the libel laws in science of which he has fallen foul of, the Government have announced the commencement of a working group on libel reform. ... <br/><a href=\"http://localhost/news/1/fighting-for-libel-reform/\" title=\"Read more\" id=\"readmorelink\">Read more</a>"
		);
	}

	public function testGetShortDesc()
	{
		$article = new \FelixOnline\Core\Article(1);
		$this->assertEquals(
			$article->getShortDesc(),
			"As Imperial alumnus Simon Singh prepares to return to College to give a guest le"
		);
	}

	public function testGetShortDescFromContent()
	{
		$article = new \FelixOnline\Core\Article(2);
		$this->assertEquals(
			$article->getShortDesc(),
			"All I bloody hear is the clock ticking. We all know what that sounds like, and w"
		);
	}

	public function testGetAuthors()
	{
		$article = new \FelixOnline\Core\Article(1);
		$authors = $article->getAuthors();

		$this->assertCount(1, $authors);
		$this->assertInstanceOf('FelixOnline\Core\User', $authors[0]);
		$this->assertEquals($authors[0]->getUser(), 'felix');
	}

	public function testGetAuthorsEnglish()
	{
		$article = new \FelixOnline\Core\Article(1);
		$authors = $article->getAuthorsEnglish();

		$this->assertEquals($authors, '<a href="http://localhost/user/felix/">Joseph Letts - Felix Editor</a>');
	}

	public function testGetAuthorsEnglishMultiple()
	{
		$article = new \FelixOnline\Core\Article(2);
		$authors = $article->getAuthorsEnglish();

		$this->assertEquals($authors, '<a href="http://localhost/user/felix/">Joseph Letts - Felix Editor</a> and <a href="http://localhost/user/jk708/">Jonathan Kim</a>');
	}

	public function testGetApprovedBy()
	{
		$article = new \FelixOnline\Core\Article(1);
		$user = $article->getApprovedby();

		$this->assertInstanceOf('FelixOnline\Core\User', $user);
		$this->assertEquals($user->getUser(), 'felix');
	}

	public function testSetContent()
	{
		$this->assertEquals(3, $this->getConnection()->getRowCount('text_story'));
		$article = new \FelixOnline\Core\Article(1);

		$article->setContent('Foo bar');
		$this->assertEquals(4, $this->getConnection()->getRowCount('text_story'));

		$app = \FelixOnline\Core\App::getInstance();
		$insert_id = $app['db']->dbh->insert_id;
		$this->assertEquals($insert_id, $article->getText1()->getId());
	}

	public function testAddAuthors()
	{
		$this->assertEquals(4, $this->getConnection()->getRowCount('article_author'));

		$article = new \FelixOnline\Core\Article(1);
		$users = array(
			new \FelixOnline\Core\User('jk708'),
			new \FelixOnline\Core\User('pk1811'),
		);
		$article->addAuthors($users);

		$this->assertEquals(6, $this->getConnection()->getRowCount('article_author'));
		$this->assertCount(3, $article->getAuthors());
	}

	public function testGetNumComments()
	{
		$article = new \FelixOnline\Core\Article(1);

		$this->assertEquals(5, $article->getNumComments());
	}

	public function testGetComments()
	{
		$article = new \FelixOnline\Core\Article(1);

		$comments = $article->getComments('0.0.0.0');

		$this->assertCount(5, $comments);
		$this->assertInstanceOf('FelixOnline\Core\Comment', $comments[0]);
	}

	public function testGetCommentsNoIP()
	{
		$article = new \FelixOnline\Core\Article(1);

		$comments = $article->getComments();

		$this->assertCount(5, $comments);
		$this->assertInstanceOf('FelixOnline\Core\Comment', $comments[0]);
	}

	public function testGetCommentsNoComments()
	{
		$article = new \FelixOnline\Core\Article(3);

		$comments = $article->getComments();
		$this->assertEmpty($comments);
	}

	public function testGetImage()
	{
		$article = new \FelixOnline\Core\Article(1);

		$image = $article->getImage();
		$this->assertInstanceOf('FelixOnline\Core\Image', $image);

		// No image
		$article = new \FelixOnline\Core\Article(2);

		$image = $article->getImage();
		$this->assertNull($image);
	}

	public function testLogVisit()
	{
		$article = new \FelixOnline\Core\Article(1);

		$article->logVisit();

		$this->assertEquals(3, $this->getConnection()->getRowCount('article_visit'));

		// Get article hit count
		$pdo = $this->getConnection()->getConnection();

		$stm = $pdo->prepare("SELECT COUNT(article) AS hits FROM article_visit WHERE repeat_visit = 0 AND article = :id");
		$stm->execute(array(':id' => 1));
		$row = $stm->fetch();

		$this->assertEquals((int) $row['hits'], 2);
	}

	public function testLogVisitLoggedIn()
	{
		$app = \FelixOnline\Core\App::getInstance();

		loginUser('felix');

		$article = new \FelixOnline\Core\Article(1);

		$article->logVisit();

		$this->assertEquals(3, $this->getConnection()->getRowCount('article_visit'));

		$pdo = $this->getConnection()->getConnection();
		$stm = $pdo->prepare("SELECT COUNT(article) AS hits FROM article_visit WHERE repeat_visit = 0 AND article = :id");
		$stm->execute(array(':id' => 1));
		$row = $stm->fetch();

		$this->assertEquals((int) $row['hits'], 2);
	}

	public function testLogVisitRepeat()
	{
		$app = \FelixOnline\Core\App::getInstance();
		$pdo = $this->getConnection()->getConnection();

		$article = new \FelixOnline\Core\Article(1);
		$article->logVisit();

		$stm = $pdo->prepare("SELECT COUNT(article) AS hits FROM article_visit WHERE repeat_visit = 0 AND article = :id");
		$stm->execute(array(':id' => 1));
		$row = $stm->fetch();

		$this->assertEquals((int) $row['hits'], 2);

		$this->assertEquals(3, $this->getConnection()->getRowCount('article_visit'));

		$stm2 = $pdo->prepare("SELECT COUNT(*) as count FROM article_visit WHERE repeat_visit = 1");
		$stm2->execute(array());
		$row = $stm2->fetch();

		$this->assertEquals((int) $row['count'], 1);

		$article->logVisit();
		$this->assertEquals(4, $this->getConnection()->getRowCount('article_visit'));

		$stm->execute(array(':id' => 1));
		$row = $stm->fetch();

		$this->assertEquals((int) $row['hits'], 2);

		$stm2 = $pdo->prepare("SELECT COUNT(*) as count FROM article_visit WHERE repeat_visit = 1");
		$stm2->execute(array());
		$row = $stm2->fetch();

		$this->assertEquals((int) $row['count'], 2);
	}
}
