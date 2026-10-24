<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node;

use PeServer\Core\Encoding;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Fragment;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAreaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLBrAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLHtmlAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLImgAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLMetaAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLScriptAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLSourceAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLStyleAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HtmlGlobalAttributes;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\CharSet;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ScriptType;
use PeServer\Core\Mvc\Template\Node\Html\CssSourceNode;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\JavaScriptSourceNode;
use PeServer\Core\Mvc\Template\Node\Html\JsonDataNode;
use PeServer\Core\Mvc\Template\Node\TextNode;
use PeServer\Core\Regex;
use PeServer\Core\Serialization\JsonSerializer;
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
			new TextNode('x')
		);
		$this->assertSame(self::DOCTYPE . '<div id="a">x</div>', $h->build($node));
	}

	public function test_attribute_escape(): void
	{
		$h = new HtmlBuilder();
		$node = $h->div(
			new HtmlGlobalAttributes(title: 'a&b<c>"d\'e&amp;'),
			new TextNode('<&>')
		);
		$this->assertSame(self::DOCTYPE . '<div title="a&amp;b&lt;c&gt;&quot;d\'e&amp;amp;">&lt;&amp;&gt;</div>', $h->build($node));
	}

	public function test_data_escape(): void
	{
		$h = new HtmlBuilder();
		$node = $h->div(
			new HtmlGlobalAttributes(data: new DataAttributes(['k' => 'a&"b'])),
			new TextNode('')
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
		$node = $h->script(new HTMLScriptAttributes(src: 'a.js', defer: true, async: false));
		$this->assertSame(self::DOCTYPE . '<script defer src="a.js"></script>', $h->build($node));
	}

	public function test_script_content(): void
	{
		$h = new HtmlBuilder();
		$node = $h->script(new HTMLScriptAttributes(), new JavaScriptSourceNode('if (a && b < c) {}'));
		$this->assertSame(self::DOCTYPE . '<script>if (a && b < c) {}</script>', $h->build($node));
	}

	public function test_script_module(): void
	{
		$h = new HtmlBuilder();
		$node = $h->script(new HTMLScriptAttributes(type: ScriptType::module), new JavaScriptSourceNode('a'));
		$this->assertSame(self::DOCTYPE . '<script type="module">a</script>', $h->build($node));
	}

	public function test_script_content_end_tag(): void
	{
		$this->expectException(ArgumentException::class);
		new JavaScriptSourceNode('a</SCRIPT><b>');
	}

	public function test_script_content_comment(): void
	{
		$this->expectException(ArgumentException::class);
		new JavaScriptSourceNode('a<!--b');
	}

	public function test_script_type_mismatch_js(): void
	{
		$h = new HtmlBuilder();
		$this->expectException(ArgumentException::class);
		$h->script(new HTMLScriptAttributes(type: ScriptType::json), new JavaScriptSourceNode('a'));
	}

	public function test_script_json(): void
	{
		$h = new HtmlBuilder();
		$node = $h->script(
			new HTMLScriptAttributes(type: ScriptType::json),
			new JsonDataNode(['a' => '</script><!-- & \' "', 'b' => 1])
		);
		$this->assertSame(
			self::DOCTYPE . '<script type="application/json">{"a":"\u003C\/script\u003E\u003C!-- \u0026 \u0027 \u0022","b":1}</script>',
			$h->build($node)
		);
	}

	public function test_script_type_mismatch_json(): void
	{
		$h = new HtmlBuilder();
		$this->expectException(ArgumentException::class);
		$h->script(new HTMLScriptAttributes(), new JsonDataNode([]));
	}

	public function test_script_json_unsafe_serializer(): void
	{
		$this->expectException(ArgumentException::class);
		new JsonDataNode(['a' => '<!--'], new JsonSerializer(JsonSerializer::SAVE_NONE));
	}

	public function test_script_json_empty_string_value(): void
	{
		$node = new JsonDataNode(['']);
		$this->assertSame('[""]', $node->toString(0, BuildOptions::default()));
	}

	public function test_style(): void
	{
		$h = new HtmlBuilder();
		$node = $h->style(new HTMLStyleAttributes(), new CssSourceNode('a > b { content: "&"; }'));
		$this->assertSame(self::DOCTYPE . '<style>a > b { content: "&"; }</style>', $h->build($node));
	}

	public function test_style_content_end_tag(): void
	{
		$this->expectException(ArgumentException::class);
		new CssSourceNode('a</style>');
	}

	public function test_source_inject_regex(): void
	{
		$regex = new Regex(Encoding::getUtf8());
		$this->assertSame('a { }', (new CssSourceNode('a { }', $regex))->toString(0, BuildOptions::default()));
		$this->assertSame('a', (new JavaScriptSourceNode('a', $regex))->toString(0, BuildOptions::default()));
		$this->assertSame('{"a":1}', (new JsonDataNode(['a' => 1], null, $regex))->toString(0, BuildOptions::default()));

		$this->expectException(ArgumentException::class);
		new CssSourceNode('a</style>', $regex);
	}

	public function test_page(): void
	{
		$h = new HtmlBuilder();
		$node = $h->html(
			new HTMLHtmlAttributes(),
			new Fragment([
				$h->head(new HtmlGlobalAttributes(), new Fragment([
					$h->title(new HtmlGlobalAttributes(), new TextNode('T')),
				])),
				$h->body(new HtmlGlobalAttributes(), new Fragment([])),
			])
		);
		$this->assertSame(self::DOCTYPE . '<html><head><title>T</title></head><body></body></html>', $h->build($node));
	}
}
