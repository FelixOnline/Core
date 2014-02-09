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
		'comments_ext',
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

	public function testUserLikedComment()
	{
		$comment = new \FelixOnline\Core\Comment(1);
		$this->assertTrue($comment->userLikedComment('felix'));
	}
}
