<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Components\Markdown;

use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Html\Components\Markdown\Markdown;
use PeServer\Core\Mvc\Template\Node\Html\Components\Markdown\MarkdownProps;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class MarkdownTest extends TestClass
{
	private function render(MarkdownProps $props): string
	{
		$context = new HtmlNodeContext(
			new ProgramContext('', '', ''),
			$this->createStub(IUrlHelper::class),
			$this->createStub(Stores::class),
			$this->createStub(Environment::class),
			new WebSecurity(),
			new HtmlBuilder()
		);
		return (new Markdown($props, $context))->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, ''));
	}

	public static function provider_class(): array
	{
		return [
			['', 'markdown'],
			['  ', 'markdown'],
			['preview', 'markdown preview'],
			['markdown markdown-browser', 'markdown markdown-browser'],
			['preview"<', 'markdown preview&quot;&lt;'],
		];
	}

	#[DataProvider('provider_class')]
	public function test_class(string $className, string $expected): void
	{
		$this->assertSame('<section class="' . $expected . '">' . "\n</section>", $this->render(new MarkdownProps('', $className)));
	}

	public function test_markdown(): void
	{
		$actual = $this->render(new MarkdownProps("# Title\n\n**bold** & text\n\n- one\n- two"));
		$this->assertStringContainsString('<h1>Title</h1>', $actual);
		$this->assertStringContainsString('<strong>bold</strong> &amp; text', $actual);
		$this->assertStringContainsString('<li>one</li>', $actual);
		$this->assertStringContainsString('<li>two</li>', $actual);
		$this->assertStringNotContainsString('&lt;h1&gt;', $actual);
	}

	public function test_markdown_extra(): void
	{
		$actual = $this->render(new MarkdownProps("Name | Value\n--- | ---\none | two\n\n```text\nsample\n```"));
		$this->assertStringContainsString('<table>', $actual);
		$this->assertStringContainsString('<td>two</td>', $actual);
		$this->assertStringContainsString('<pre class="source"><code class="hljs text">', $actual);
		$this->assertStringContainsString('<span class="code-line " data-line=1>sample</span>', $actual);
	}
}
