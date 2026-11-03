<?php

declare(strict_types=1);

namespace PeServerUT\App\Views\Pages\Home;

use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Pages\Home\HomeIndexPage;
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
use PHPUnit\Framework\Attributes\DataProvider;

class HomeIndexPageTest extends TestClass
{
	public static function provider_children(): array
	{
		return [
			['development', true, false],
			['production', false, true],
			['test', false, false],
		];
	}

	#[DataProvider('provider_children')]
	public function test_children(string $environmentName, bool $development, bool $production): void
	{
		$environment = $this->createStub(Environment::class);
		$environment->method('get')->willReturn($environmentName);
		$environment->method('isDevelopment')->willReturn($development);
		$environment->method('isProduction')->willReturn($production);
		$context = new HtmlNodeContext(
			new ProgramContext('', '', ''),
			$this->createStub(IUrlHelper::class),
			$this->createStub(Stores::class),
			$environment,
			new WebSecurity(),
			new HtmlBuilder()
		);
		$page = new HomeIndexPage(
			new RootProps(new TemplateParameter(HttpStatus::OK, [], [])),
			$context
		);
		$layout = $this->callInstanceMethod($page, 'getNode');
		$this->assertInstanceOf(DefaultPage::class, $layout);
		$this->assertSame('トップ', $layout->props->title);
		$this->assertNotNull($layout->props->children);
		$html = $layout->props->children->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, ''));

		$this->assertStringContainsString('<p>Pe のサーバーが必要な処理。</p>', $html);
		foreach (
			[
				'/plugin2' => 'プラグイン',
				'/api-doc' => 'API',
				'/public/api-doc/' => 'Doc: PeServer',
				'/public/coverage/php/uit/' => 'Code Coverage: PeServer',
				'/public/coverage/script/lcov-report/' => 'Code Coverage: Script',
				'/tool' => 'ツールとか',
			] as $href => $label
		) {
			$this->assertStringContainsString('<a href="' . $href . '">' . $label . '</a>', $html);
		}
		$this->assertSame(4, substr_count($html, '<tr>'));
		$this->assertStringContainsString('<th rowspan="2">Pe</th>', $html);
		$this->assertStringContainsString('<th rowspan="2">Pe.Server</th>', $html);
		foreach (
			[
				'https://github.com/sk-0520/Pe/actions/workflows/build-ci-only.yml' => 'https://github.com/sk-0520/Pe/actions/workflows/build-ci-only.yml/badge.svg',
				'https://github.com/sk-0520/Pe/actions/workflows/build-ci-cd.yml' => 'https://github.com/sk-0520/Pe/actions/workflows/build-ci-cd.yml/badge.svg?branch=master',
				'https://github.com/sk-0520/Pe.Server/actions' => 'https://github.com/sk-0520/Pe.Server/actions/workflows/build-works.yml/badge.svg',
				'https://github.com/sk-0520/Pe.Server/actions?query=branch%3Amaster' => 'https://github.com/sk-0520/Pe.Server/actions/workflows/build-works.yml/badge.svg?branch=master',
			] as $href => $src
		) {
			$this->assertStringContainsString('<a href="' . $href . '"><img', $html);
			if ($production) {
				$this->assertStringContainsString('src="' . $src . '"', $html);
			}
		}
		$this->assertSame($production ? 4 : 0, substr_count($html, 'src='));
		$this->assertSame(4, substr_count($html, 'class="page-user-index-ci ' . $environmentName . '"'));
		foreach (['<h2>dev</h2>', '<a href="/index2">index2</a>', '/dev/exception', '/ajax/dev/exception/json', '/dev/streaming'] as $value) {
			if ($development) {
				$this->assertStringContainsString($value, $html);
			} else {
				$this->assertStringNotContainsString($value, $html);
			}
		}
	}
}
