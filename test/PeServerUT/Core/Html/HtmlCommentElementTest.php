<?php

declare(strict_types=1);

namespace PeServerUT\Core\Html;

use PeServer\Core\Html\HtmlDocument;
use PeServerTest\TestClass;

class HtmlCommentElementTest extends TestClass
{
	public function test_set_get()
	{
		$hd = new HtmlDocument();
		$comment = $hd->addComment('abc');
		$this->assertSame('abc', $comment->get());

		$comment->set('<xyz>');
		$this->assertSame('<xyz>', $comment->get());
	}
}
