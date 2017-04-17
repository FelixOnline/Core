<?php

require_once __DIR__ . '/../../AppTestCase.php';

class CategoryTest extends AppTestCase
{
    public $fixtures = array(
        'articles',
        'categories',
        'users',
        'category_authors',
        'audit_log'
    );

    public function testGetURL()
    {
        $category = new \FelixOnline\Core\Category(1);

        $this->assertEquals($category->getURL(), 'http://localhost/news/');
    }

    public function testGetURLPage()
    {
        $category = new \FelixOnline\Core\Category(1);

        $this->assertEquals($category->getURL(2), 'http://localhost/news/2/');
    }

    public function testGetEditors()
    {
        $category = new \FelixOnline\Core\Category(1);

        $editors = $category->getEditors();

        $this->assertCount(2, $editors);
        $this->assertInstanceOf('FelixOnline\Core\User', $editors[0]);
        $this->assertEquals($editors[0]->getUser(), 'felix');
    }

    public function testGetEditorsNull()
    {
        $category = new \FelixOnline\Core\Category(2);

        $editors = $category->getEditors();
        $this->assertNull($editors);
    }

    public function testGetChildren()
    {
        $category = new \FelixOnline\Core\Category(1);

        $children = $category->getChildren();

        $this->assertCount(1, $children);
        $this->assertInstanceOf('FelixOnline\Core\Category', $children[0]);
        $this->assertEquals($children[0]->getLabel(), 'Comment');
    }

    public function testGetChildrenNull()
    {
        $category = new \FelixOnline\Core\Category(2);

        $children = $category->getChildren();
        $this->assertNull($children);
    }

    public function testGetCategories()
    {
        $categories = \FelixOnline\Core\Category::getCategories();

        $this->assertCount(2, $categories);
        $this->assertInstanceOf('FelixOnline\Core\Category', $categories[0]);
        $this->assertEquals($categories[0]->getLabel(), 'News');
    }

    public function testGetRootCategories()
    {
        $categories = \FelixOnline\Core\Category::getRootCategories();

        $this->assertCount(1, $categories);
        $this->assertInstanceOf('FelixOnline\Core\Category', $categories[0]);
        $this->assertEquals($categories[0]->getLabel(), 'News');
    }

    public function testSecretCategory()
    {
        $this->setExpectedException(
            'FelixOnline\Exceptions\ModelNotFoundException',
            'This is a secret category and you don\'t have permission to access it'
        );

        $category = new \FelixOnline\Core\Category(3);
    }
}
