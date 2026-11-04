<?php

declare(strict_types=1);

namespace PeServerUT\App\Views\Pages\Error;

use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Pages\Error\ErrorPage;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\TestClass;

class ErrorPageTest extends TestClass
{
	public function test_error_details(): void
	{
		$environment = $this->createStub(Environment::class);
		$environment->method('isDevelopment')->willReturn(false);
		$context = new HtmlNodeContext(
			new ProgramContext('', '', ''),
			$this->createStub(IUrlHelper::class),
			$this->createStub(Stores::class),
			$environment,
			new WebSecurity(),
			new HtmlBuilder()
		);
		$page = new ErrorPage(
			new RootProps(new TemplateParameter(HttpStatus::NotFound, [
				'error_number' => 404,
				'message' => '<not found>',
			], [])),
			$context
		);
		$layout = $this->callInstanceMethod($page, 'getNode');

		$this->assertInstanceOf(DefaultPage::class, $layout);
		$this->assertSame('エラー: ' . HttpStatus::NotFound->value, $layout->props->title);
		$this->assertNotNull($layout->props->children);
		$html = $layout->props->children->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, ''));

		$this->assertStringContainsString('問題が発生しました。', $html);
		$this->assertStringContainsString('data-clipboard="inline">404</code>', $html);
		$this->assertStringContainsString('&lt;not found&gt;', $html);
		$this->assertStringNotContainsString('<not found>', $html);
	}

	public function test_empty_message_uses_fallback(): void
	{
		$environment = $this->createStub(Environment::class);
		$environment->method('isDevelopment')->willReturn(false);
		$context = new HtmlNodeContext(
			new ProgramContext('', '', ''),
			$this->createStub(IUrlHelper::class),
			$this->createStub(Stores::class),
			$environment,
			new WebSecurity(),
			new HtmlBuilder()
		);
		$page = new ErrorPage(
			new RootProps(new TemplateParameter(HttpStatus::NotFound, [
				'error_number' => 404,
				'message' => '',
			], [])),
			$context
		);
		$layout = $this->callInstanceMethod($page, 'getNode');
		$this->assertInstanceOf(DefaultPage::class, $layout);
		$this->assertNotNull($layout->props->children);
		$html = $layout->props->children->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, ''));

		$this->assertStringContainsString('<dd>なし</dd>', $html);
	}
}
