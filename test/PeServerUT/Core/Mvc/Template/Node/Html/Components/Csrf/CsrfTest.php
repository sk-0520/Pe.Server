<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Components\Csrf;

use PeServer\Core\Environment;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\Csrf;
use PeServer\Core\Mvc\Template\Node\Html\Components\Csrf\CsrfProps;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\StoreOptions;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class CsrfTest extends TestClass
{
	private function render(CsrfProps $props, bool $hasToken): string
	{
		$security = new WebSecurity();
		$stores = new Stores($this->createStub(SpecialStore::class), StoreOptions::default(), $security);
		if ($hasToken) {
			$stores->session->set($security->getCsrfKind(WebSecurity::CSRF_KIND_SESSION_KEY), 'token<&"');
		}
		$context = new HtmlNodeContext(
			new ProgramContext('', '', ''),
			$this->createStub(IUrlHelper::class),
			$stores,
			$this->createStub(Environment::class),
			$security,
			new HtmlBuilder()
		);
		return (new Csrf($props, $context))->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, ''));
	}

	public static function provider_build(): array
	{
		return [
			[new CsrfProps(), true, '<input type="hidden" name="core__csrf_name" value="token&lt;&amp;&quot;" />'],
			[new CsrfProps('id'), true, '<meta content="token&lt;&amp;&quot;" name="core__csrf_id" id="core__csrf_id" />'],
			[new CsrfProps(), false, ''],
			[new CsrfProps('id'), false, ''],
		];
	}

	#[DataProvider('provider_build')]
	public function test_build(CsrfProps $props, bool $hasToken, string $expected): void
	{
		$this->assertSame($expected, $this->render($props, $hasToken));
	}
}
