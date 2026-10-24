<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Mvc\Template\Node\Content;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAreaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBrAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLImgAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLScriptAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLSourceAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\CharSet;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\TextContent;
use PeServer\Core\Throws\ArgumentException;
use PeServerTest\TestClass;

class HtmlBuilderTest extends TestClass
{
	private const DOCTYPE = '<!DOCTYPE html>' . PHP_EOL;

	public function test_simple(): void
	{
		$h = new HtmlBuilder();
		$node = $h->div(
			new HtmlGlobalAttributes(id: 'a'),
			new TextContent('x')
		);
		$this->assertSame(self::DOCTYPE . '<div id="a">x</div>', $h->build($node));
	}

	public function test_attribute_escape(): void
	{
		$h = new HtmlBuilder();
		$node = $h->div(
			new HtmlGlobalAttributes(title: 'a&b<c>"d\'e&amp;'),
			new TextContent('<&>')
		);
		$this->assertSame(self::DOCTYPE . '<div title="a&amp;b&lt;c&gt;&quot;d\'e&amp;amp;">&lt;&amp;&gt;</div>', $h->build($node));
	}

	public function test_data_escape(): void
	{
		$h = new HtmlBuilder();
		$node = $h->div(
			new HtmlGlobalAttributes(data: new DataAttributes(['k' => 'a&"b'])),
			new TextContent('')
		);
		$this->assertSame(self::DOCTYPE . '<div data-k="a&amp;&quot;b"></div>', $h->build($node));
	}

	public function test_void(): void
	{
		$h = new HtmlBuilder();
		$this->assertSame(self::DOCTYPE . '<meta charset="UTF-8" />', $h->build($h->meta(new HTMLMetaAttributes(charset: CharSet::Utf8))));
		$this->assertStringNotContainsString('</img>', $h->build($h->img(new HTMLImgAttributes(src: 'a.png'))));
		$this->assertStringEndsWith('<area />', $h->build($h->area(new HTMLAreaAttributes())));
		$this->assertStringEndsWith('<source />', $h->build($h->source(new HTMLSourceAttributes())));
		$this->assertStringEndsWith('<br />', $h->build($h->br(new HTMLBrAttributes())));
	}

	public function test_script_bool(): void
	{
		$h = new HtmlBuilder();
		$node = $h->script(new HTMLScriptAttributes(src: 'a.js', defer: true, async: false), new Content([]));
		$this->assertSame(self::DOCTYPE . '<script defer src="a.js"></script>', $h->build($node));
	}

	public function test_script_content(): void
	{
		$h = new HtmlBuilder();
		$node = $h->script(new HTMLScriptAttributes(), new TextContent('if (a && b < c) {}'));
		$this->assertSame(self::DOCTYPE . '<script>if (a && b < c) {}</script>', $h->build($node));
	}

	public function test_script_content_end_tag(): void
	{
		$h = new HtmlBuilder();
		$node = $h->script(new HTMLScriptAttributes(), new TextContent('a</SCRIPT><b>'));
		$this->expectException(ArgumentException::class);
		$h->build($node);
	}

	public function test_page(): void
	{
		$h = new HtmlBuilder();
		$node = $h->html(
			new HTMLHtmlAttributes(),
			new Content([
				$h->head(new HtmlGlobalAttributes(), new Content([
					$h->title(new HtmlGlobalAttributes(), new TextContent('T')),
				])),
				$h->body(new HtmlGlobalAttributes(), new Content([])),
			])
		);
		$this->assertSame(self::DOCTYPE . '<html><head><title>T</title></head><body></body></html>', $h->build($node));
	}
}
