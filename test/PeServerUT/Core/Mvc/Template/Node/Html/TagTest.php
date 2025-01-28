<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html;

use PeServerTest\TestClass;
use PeServer\Core\Mvc\Template\Node\Html\Tag;

class TagTest extends TestClass
{
	#region function

	public function test_html()
	{
		$tag = new Tag();
		$actual = $tag->html();
		$this->assertSame('html', $actual->tagName);
	}

	#endregion
}
