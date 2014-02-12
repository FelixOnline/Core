<?php

require_once __DIR__ . '/../../AppTestCase.php';
require_once __DIR__ . '/../../../constants.php';

class CommentTest extends AppTestCase
{
	public $fixtures = array(
		'articles',
		'article_authors',
		'users',
		'comments',
		'comment_likes',
		'categories',
	);

	public function testInteralComment()
	{
		$comment = new \FelixOnline\Core\Comment(1);

		$this->assertEquals($comment->getContent(), 'Test comment number one');
	}

	public function testExternalComment()
	{
		$comment = new \FelixOnline\Core\Comment(80000001);

		$this->assertEquals($comment->getContent(), 'Test ext comment');
	}

	public function testGetArticle()
	{
		$comment = new \FelixOnline\Core\Comment(1);
		$article = $comment->getArticle();

		$this->assertInstanceOf('FelixOnline\Core\Article', $article);
		$this->assertEquals($article->getTitle(), 'Fighting for Libel Reform');
	}

	public function testGetUser()
	{
		$comment = new \FelixOnline\Core\Comment(1);
		$user = $comment->getUser();

		$this->assertInstanceOf('FelixOnline\Core\User', $user);
		$this->assertEquals($user->getUser(), 'felix');
	}

	public function testGetUserException()
	{
		$comment = new \FelixOnline\Core\Comment(80000001);

		$this->setExpectedException(
			'FelixOnline\Exceptions\InternalException',
			'External comment does not have a user'
		);

		$user = $comment->getUser();
	}

	public function testGetReply()
	{
		$comment = new \FelixOnline\Core\Comment(1);
		$this->assertNull($comment->getReply());

		$comment = new \FelixOnline\Core\Comment(2);
		$reply = $comment->getReply();
		$this->assertInstanceOf('FelixOnline\Core\Comment', $reply);
		$this->assertEquals($reply->getContent(), 'Test comment number one');
	}

	public function testGetContent()
	{
		$comment = new \FelixOnline\Core\Comment(1);
		$this->assertEquals($comment->getContent(), 'Test comment number one');
	}

	public function testGetName()
	{
		$comment = new \FelixOnline\Core\Comment(1);
		$this->assertEquals($comment->getName(), 'Joseph Letts - Felix Editor');
	}

	public function testGetNameExternal()
	{
		$comment = new \FelixOnline\Core\Comment(80000001);
		$this->assertEquals($comment->getName(), 'Test');
	}

	public function testGetNameAnonymous()
	{
		$comment = new \FelixOnline\Core\Comment(80000005);
		$this->assertEquals($comment->getName(), 'Anonymous');
	}

	public function testGetURL()
	{
		$comment = new \FelixOnline\Core\Comment(1);
		$this->assertEquals($comment->getURL(), 'http://localhost/news/1/fighting-for-libel-reform/#comment1');
	}

	public function testByAuthor()
	{
		$comment = new \FelixOnline\Core\Comment(1);
		$this->assertTrue($comment->byAuthor());

		$comment = new \FelixOnline\Core\Comment(2);
		$this->assertFalse($comment->byAuthor());

		$comment = new \FelixOnline\Core\Comment(80000001);
		$this->assertFalse($comment->byAuthor());
	}

	public function testIsRejected()
	{
		$internal = new \FelixOnline\Core\Comment(1);
		$this->assertFalse($internal->isRejected());

		$external = new \FelixOnline\Core\Comment(80000001);
		$this->assertFalse($external->isRejected());

		$external = new \FelixOnline\Core\Comment(80000003);
		$this->assertTrue($external->isRejected());
	}

	public function testIsPending()
	{
		$internal = new \FelixOnline\Core\Comment(1);
		$this->assertFalse($internal->isPending());

		$external = new \FelixOnline\Core\Comment(80000001);
		$this->assertFalse($external->isPending());

		$external = new \FelixOnline\Core\Comment(80000004);
		$this->assertTrue($external->isPending());
	}

	public function testUserLikedComment()
	{
		$comment = new \FelixOnline\Core\Comment(1);
		$this->assertTrue($comment->userLikedComment('felix'));
	}

	public function testNewExternalComment()
	{
		$faker = Faker\Factory::create();
		$name = $faker->name;
		$email = $faker->email;
		$content = $faker->text;

		$this->assertEquals(7, $this->getConnection()->getRowCount('comment'));

		$article = new \FelixOnline\Core\Article(1);
		$comment = new \FelixOnline\Core\Comment();

		$comment->setExternal(1)
			->setName($name)
			->setComment($content)
			->setEmail($email)
			->setArticle($article)
			->save();

		$this->assertEquals(8, $this->getConnection()->getRowCount('comment'));

		// Get comment details
		$pdo = $this->getConnection()->getConnection();

		$stm = $pdo->prepare("SELECT * FROM comment WHERE id = :id");
		$stm->execute(array(':id' => $comment->getId()));
		$row = $stm->fetch();

		$this->assertEquals($row['name'], $name);
		$this->assertEquals($row['email'], $email);
		$this->assertEquals($row['comment'], $content);
		$this->assertNull($row['user']);
		$this->assertEquals((int) $row['external'], 1);
		$this->assertEquals((int) $row['active'], 1);
		$this->assertEquals((int) $row['pending'], 1);
		$this->assertEquals((int) $row['spam'], 0);
		$this->assertEquals((int) $row['likes'], 0);
		$this->assertEquals((int) $row['dislikes'], 0);
	}

	public function testNewInternalComment()
	{
		$faker = Faker\Factory::create();
		$content = $faker->text;

		$this->assertEquals(7, $this->getConnection()->getRowCount('comment'));

		$article = new \FelixOnline\Core\Article(1);
		$user = new \FelixOnline\Core\User('felix');
		$comment = new \FelixOnline\Core\Comment();

		$comment->setExternal(0)
			->setUser($user)
			->setComment($content)
			->setArticle($article)
			->save();

		$this->assertEquals(8, $this->getConnection()->getRowCount('comment'));

		// Get comment details
		$pdo = $this->getConnection()->getConnection();

		$stm = $pdo->prepare("SELECT * FROM comment WHERE id = :id");
		$stm->execute(array(':id' => $comment->getId()));
		$row = $stm->fetch();

		$this->assertEquals($row['user'], $user->getUser());
		$this->assertEquals($row['comment'], $content);
		$this->assertNull($row['name']);
		$this->assertNull($row['email']);
		$this->assertEquals((int) $row['external'], 0);
		$this->assertEquals((int) $row['active'], 1);
		$this->assertEquals((int) $row['pending'], 0);
		$this->assertEquals((int) $row['spam'], 0);
		$this->assertEquals((int) $row['likes'], 0);
		$this->assertEquals((int) $row['dislikes'], 0);
	}

	public function testSpamComment()
	{
		$faker = Faker\Factory::create();
		$name = "viagra-test-123";
		$email = $faker->email;
		$content = $faker->text;

		$this->assertEquals(7, $this->getConnection()->getRowCount('comment'));

		$article = new \FelixOnline\Core\Article(1);
		$comment = new \FelixOnline\Core\Comment();

		$comment->setExternal(1)
			->setName($name)
			->setComment($content)
			->setEmail($email)
			->setArticle($article)
			->save();

		$this->assertEquals(8, $this->getConnection()->getRowCount('comment'));

		// Get comment details
		$pdo = $this->getConnection()->getConnection();

		$stm = $pdo->prepare("SELECT * FROM comment WHERE id = :id");
		$stm->execute(array(':id' => $comment->getId()));
		$row = $stm->fetch();

		$this->assertEquals($row['name'], $name);
		$this->assertEquals($row['email'], $email);
		$this->assertEquals($row['comment'], $content);
		$this->assertNull($row['user']);
		$this->assertEquals((int) $row['external'], 1);
		$this->assertEquals((int) $row['active'], 0);
		$this->assertEquals((int) $row['pending'], 0);
		$this->assertEquals((int) $row['spam'], 1);
		$this->assertEquals((int) $row['likes'], 0);
		$this->assertEquals((int) $row['dislikes'], 0);
	}

	public function testCommentExists()
	{
		$article = new \FelixOnline\Core\Article(1);
		$comment = new \FelixOnline\Core\Comment();

		$faker = Faker\Factory::create();
		$name = $faker->name;
		$email = $faker->email;
		$content = $faker->text;

		$comment->setExternal(1)
			->setName($name)
			->setComment($content)
			->setEmail($email)
			->setArticle($article);

		$this->assertFalse($comment->commentExists());

		$comment->save();

		$duplicate = new \FelixOnline\Core\Comment();

		$duplicate->setExternal(1)
			->setName($name)
			->setComment($content)
			->setArticle($article);

		$this->assertTrue($duplicate->commentExists());
	}

	public function testInteralCommentExists()
	{
		$article = new \FelixOnline\Core\Article(1);
		$user = new \FelixOnline\Core\User('felix');
		$comment = new \FelixOnline\Core\Comment();

		$faker = Faker\Factory::create();
		$content = $faker->text;

		$comment->setExternal(0)
			->setUser($user)
			->setComment($content)
			->setArticle($article);

		$this->assertFalse($comment->commentExists());

		$comment->save();

		$duplicate = new \FelixOnline\Core\Comment();

		$duplicate->setExternal(0)
			->setUser($user)
			->setComment($content)
			->setArticle($article);

		$this->assertTrue($duplicate->commentExists());
	}

	public function testSendExtEmail()
	{
		$faker = Faker\Factory::create();
		$name = $faker->name;
		$email = $faker->email;
		$content = $faker->text;
		$article = new \FelixOnline\Core\Article(1);
		$comment = new \FelixOnline\Core\Comment();

		$test = $this;

		$emailMock = $this->mock('\Swift_Mailer')
			->send(function($message) use ($test, $article) {
				$test->assertGreaterThanOrEqual(0, strpos($message->getSubject(), $article->getTitle()));
				$test->assertArrayHasKey('jkimbo@gmail.com', $message->getTo());

				return true;
			}, $this->once())
			->new();

		$this->app['email'] = $emailMock;

		$comment->setExternal(1)
			->setName($name)
			->setComment($content)
			->setEmail($email)
			->setArticle($article)
			->save();
	}
}
