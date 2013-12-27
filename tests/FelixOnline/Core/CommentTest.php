<?php

require_once __DIR__ . '/../../DatabaseTestCase.php';
require_once __DIR__ . '/../../utilities.php';
require_once __DIR__ . '/../../../constants.php';

class CommentTest extends DatabaseTestCase
{
	public $fixtures = array(
		'articles',
		'article_authors',
		'users',
		'comments',
		'comments_ext',
	);

	public function setUp()
	{
		parent::setUp();
		create_app(array(
			'base_url' => 'http://localhost/'
		));
	}

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
}
