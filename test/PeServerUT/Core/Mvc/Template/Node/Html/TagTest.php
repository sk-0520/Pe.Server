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

	public function test_bdo()
	{
		$tag = new Tag();
		$actual = $tag->bdo();
		$this->assertSame('<bdo></bdo>', (string)$actual);
	}

	public function test_br()
	{
		$tag = new Tag();
		$actual = $tag->br();
		$this->assertSame('<br />', (string)$actual);
	}

	public function test_blockquote()
	{
		$tag = new Tag();
		$actual = $tag->blockquote();
		$this->assertSame('<blockquote></blockquote>', (string)$actual);
	}

	public function test_button()
	{
		$tag = new Tag();
		$actual = $tag->button();
		$this->assertSame('<button></button>', (string)$actual);
	}

	public function test_canvas()
	{
		$tag = new Tag();
		$actual = $tag->canvas();
		$this->assertSame('<canvas></canvas>', (string)$actual);
	}

	public function test_caption()
	{
		$tag = new Tag();
		$actual = $tag->caption();
		$this->assertSame('<caption></caption>', (string)$actual);
	}

	public function test_cite()
	{
		$tag = new Tag();
		$actual = $tag->cite();
		$this->assertSame('<cite></cite>', (string)$actual);
	}

	public function test_code()
	{
		$tag = new Tag();
		$actual = $tag->code();
		$this->assertSame('<code></code>', (string)$actual);
	}

	public function test_col()
	{
		$tag = new Tag();
		$actual = $tag->col();
		$this->assertSame('<col />', (string)$actual);
	}

	public function test_colgroup()
	{
		$tag = new Tag();
		$actual = $tag->colgroup();
		$this->assertSame('<colgroup></colgroup>', (string)$actual);
	}

	public function test_data()
	{
		$tag = new Tag();
		$actual = $tag->data();
		$this->assertSame('<data></data>', (string)$actual);
	}

	public function test_datalist()
	{
		$tag = new Tag();
		$actual = $tag->datalist();
		$this->assertSame('<datalist></datalist>', (string)$actual);
	}

	public function test_dd()
	{
		$tag = new Tag();
		$actual = $tag->dd();
		$this->assertSame('<dd></dd>', (string)$actual);
	}

	public function test_del()
	{
		$tag = new Tag();
		$actual = $tag->del();
		$this->assertSame('<del></del>', (string)$actual);
	}

	public function test_details()
	{
		$tag = new Tag();
		$actual = $tag->details();
		$this->assertSame('<details></details>', (string)$actual);
	}

	public function test_dfn()
	{
		$tag = new Tag();
		$actual = $tag->dfn();
		$this->assertSame('<dfn></dfn>', (string)$actual);
	}

	public function test_dialog()
	{
		$tag = new Tag();
		$actual = $tag->dialog();
		$this->assertSame('<dialog></dialog>', (string)$actual);
	}

	#endregion
}
