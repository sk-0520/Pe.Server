<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Components\Pager;

use DOMDocument;
use DOMXPath;
use PeServer\Core\Environment;
use PeServer\Core\Mvc\Pagination;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Html\Components\Pager\Pager;
use PeServer\Core\Mvc\Template\Node\Html\Components\Pager\PagerProps;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class PagerTest extends TestClass
{
	private function render(PagerProps $props): string
	{
		$context = new HtmlNodeContext(
			new ProgramContext('', '', ''),
			$this->createStub(IUrlHelper::class),
			$this->createStub(Stores::class),
			$this->createStub(Environment::class),
			new WebSecurity(),
			new HtmlBuilder()
		);
		return (new Pager($props, $context))->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, ''));
	}

	public function test_empty(): void
	{
		$this->assertSame('', $this->render(new PagerProps(new Pagination(1, 10, 0, shortJump: false, longJump: false), '/page/<page_number>')));
	}

	public function test_invalid_href(): void
	{
		$this->expectException(ArgumentException::class);
		$this->render(new PagerProps(new Pagination(1, 10, 100), ' '));
	}

	public static function provider_pages(): array
	{
		return [[1], [5], [10]];
	}

	#[DataProvider('provider_pages')]
	public function test_links(int $page): void
	{
		$html = $this->render(new PagerProps(
			new Pagination($page, 10, 100),
            '/page/<page_number>?a=1&b=2',
			jumpHead: '<first>',
            jumpTail: '<last>',
            jumpPrev: '<prev>',
            jumpNext: '<next>'
		));
		$dom = new DOMDocument();
		$this->assertTrue($dom->loadHTML('<html><body>' . $html . '</body></html>'));
		$xpath = new DOMXPath($dom);
		$this->assertSame(1, $xpath->query("//li[@class='normal current']/a[@class='current'][text()='$page']")->length);
		$this->assertSame($page === 1 || $page === 10 ? 2 : 0, $xpath->query("//li[contains(@class,'disabled')]/a[@class='disabled'][@tabindex='-1'][not(@href)]")->length);
		$this->assertSame('<first>', $xpath->evaluate('string(//li[1]/a)'));
		$this->assertSame('<prev>', $xpath->evaluate('string(//li[2]/a)'));
		$this->assertSame('<last>', $xpath->evaluate('string(//li[last()]/a)'));
		$this->assertSame('<next>', $xpath->evaluate('string(//li[last()-1]/a)'));
		foreach ($xpath->query('//a[@href]') as $link) {
			$this->assertMatchesRegularExpression('#^/page/[0-9]+\?a=1&b=2$#', $link->getAttribute('href'));
		}
		$this->assertStringNotContainsString('<first>', $html);
	}
}
