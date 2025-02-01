<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\Attributes;
use PeServer\Core\Mvc\Template\Node\Comment;
use PeServer\Core\Mvc\Template\Node\Content;
use PeServer\Core\Mvc\Template\Node\ElementOptions;
use PeServer\Core\Mvc\Template\Node\Element;
use PeServer\Core\Mvc\Template\Node\Html\Content\HtmlContent;
use PeServer\Core\Mvc\Template\Node\NoneContent;
use PeServer\Core\Mvc\Template\Node\Props;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Throws\ArgumentException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\TestWith;

class CommentTest extends TestClass
{
	public function test_constructor_throw()
	{
		$this->expectException(ArgumentException::class);
		new Comment("--");
		$this->fail();
	}

	public function test_single0()
	{
		$actual = new Comment("");
		$this->assertSame("<!-- -->", (string)$actual);
	}

	public function test_single1()
	{
		$actual = new Comment("comment");
		$this->assertSame("<!-- comment -->", (string)$actual);
	}

	public function test_single2()
	{
		$actual = new Comment("  comment  ");
		$this->assertSame("<!-- comment -->", (string)$actual);
	}

	#[TestWith(["\n"])]
	#[TestWith(["\r"])]
	#[TestWith(["\r\n"])]
	public function test_multi1($newline)
	{
		$comment = new Comment("1{$newline}2");
		$expected = "<!--" . PHP_EOL . "1" . PHP_EOL . "2" . PHP_EOL . "-->";
		$actual = (string)$comment;
		$this->assertSame($expected, $actual);
	}

	#[TestWith(["\n"])]
	#[TestWith(["\r"])]
	#[TestWith(["\r\n"])]
	public function test_multi2($newline)
	{
		$comment = new Comment("   1    {$newline}    2    ");
		$expected = "<!--" . PHP_EOL . "1" . PHP_EOL . "2" . PHP_EOL . "-->";
		$actual = (string)$comment;
		$this->assertSame($expected, $actual);
	}
}
