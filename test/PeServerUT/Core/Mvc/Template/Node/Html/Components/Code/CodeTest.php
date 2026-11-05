<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Components\Code;

use DOMDocument;
use DOMXPath;
use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Html\Components\Code\Code;
use PeServer\Core\Mvc\Template\Node\Html\Components\Code\CodeProps;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\TestClass;

class CodeTest extends TestClass
{
	public function test_json_and_highlighted_lines(): void
	{
		$context = new HtmlNodeContext(
			new ProgramContext('', '', ''),
			$this->createStub(IUrlHelper::class),
			$this->createStub(Stores::class),
			$this->createStub(Environment::class),
			new WebSecurity(),
			new HtmlBuilder()
		);
		$source = "{\n\"value\": \"<img src=x>&\"\n}";
		$html = (new Code(new CodeProps($source, 'json', '2'), $context))->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, ''));
		$dom = new DOMDocument();
		$this->assertTrue($dom->loadHTML('<html><body>' . $html . '</body></html>'));
		$xpath = new DOMXPath($dom);
		$this->assertSame(1, $xpath->query("//pre[@class='source']/code[@class='hljs json']")->length);
		$this->assertSame(1, $xpath->query("//span[contains(@class,'strong-line')][@data-line='2']")->length);
		$this->assertSame(0, $xpath->query('//img')->length);
		$this->assertStringContainsString('"value": "<img src=x>&"', $xpath->evaluate('string(//code)'));
		$this->assertStringContainsString('&lt;img src=x&gt;&amp;', $html);
	}
}
