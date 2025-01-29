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
		$this->assertSame('<html></html>', (string)$actual);
	}

	public function test_head()
	{
		$tag = new Tag();
		$actual = $tag->head();
		$this->assertSame('<head></head>', (string)$actual);
	}

	public function test_title()
	{
		$tag = new Tag();
		$actual = $tag->title();
		$this->assertSame('<title></title>', (string)$actual);
	}

	public function test_base()
	{
		$tag = new Tag();
		$actual = $tag->base();
		$this->assertSame('<base />', (string)$actual);
	}

	public function test_link()
	{
		$tag = new Tag();
		$actual = $tag->link();
		$this->assertSame('<link />', (string)$actual);
	}

	public function test_style()
	{
		$tag = new Tag();
		$actual = $tag->style();
		$this->assertSame('<style></style>', (string)$actual);
	}

	public function test_script()
	{
		$tag = new Tag();
		$actual = $tag->script();
		$this->assertSame('<script></script>', (string)$actual);
	}

	public function test_meta()
	{
		$tag = new Tag();
		$actual = $tag->meta();
		$this->assertSame('<meta />', (string)$actual);
	}

	public function test_noscript()
	{
		$tag = new Tag();
		$actual = $tag->noscript();
		$this->assertSame('<noscript></noscript>', (string)$actual);
	}

	public function test_template()
	{
		$tag = new Tag();
		$actual = $tag->template();
		$this->assertSame('<template></template>', (string)$actual);
	}

	public function test_body()
	{
		$tag = new Tag();
		$actual = $tag->body();
		$this->assertSame('<body></body>', (string)$actual);
	}

	public function test_a()
	{
		$tag = new Tag();
		$actual = $tag->a();
		$this->assertSame('<a></a>', (string)$actual);
	}

	public function test_abbr()
	{
		$tag = new Tag();
		$actual = $tag->abbr();
		$this->assertSame('<abbr></abbr>', (string)$actual);
	}

	public function test_address()
	{
		$tag = new Tag();
		$actual = $tag->address();
		$this->assertSame('<address></address>', (string)$actual);
	}

	public function test_area()
	{
		$tag = new Tag();
		$actual = $tag->area();
		$this->assertSame('<area />', (string)$actual);
	}

	public function test_article()
	{
		$tag = new Tag();
		$actual = $tag->article();
		$this->assertSame('<article></article>', (string)$actual);
	}

	public function test_aside()
	{
		$tag = new Tag();
		$actual = $tag->aside();
		$this->assertSame('<aside></aside>', (string)$actual);
	}

	public function test_audio()
	{
		$tag = new Tag();
		$actual = $tag->audio();
		$this->assertSame('<audio></audio>', (string)$actual);
	}

	public function test_b()
	{
		$tag = new Tag();
		$actual = $tag->b();
		$this->assertSame('<b></b>', (string)$actual);
	}

	public function test_bdi()
	{
		$tag = new Tag();
		$actual = $tag->bdi();
		$this->assertSame('<bdi></bdi>', (string)$actual);
	}

	#endregion
}
