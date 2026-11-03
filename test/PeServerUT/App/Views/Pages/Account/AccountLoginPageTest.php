<?php

declare(strict_types=1);

namespace PeServerUT\App\Views\Pages\Account;

use DOMDocument;
use DOMXPath;
use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Pages\Account\AccountLoginPage;
use PeServer\Core\Environment;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Template\Node\BuildOptions;
use PeServer\Core\Mvc\Template\Node\Html\HtmlBuilder;
use PeServer\Core\Mvc\Template\Node\Html\HtmlNodeContext;
use PeServer\Core\Mvc\Template\Node\RootProps;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\ProgramContext;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\StoreOptions;
use PeServer\Core\Store\Stores;
use PeServer\Core\Web\IUrlHelper;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class AccountLoginPageTest extends TestClass
{
	public static function provider_page(): array
	{
		return [
			['development', true],
			['production', true],
			['test', true],
			['test', false],
		];
	}

	#[DataProvider('provider_page')]
	public function test_page(string $environmentName, bool $hasToken): void
	{
		$environment = $this->createStub(Environment::class);
		$environment->method('isDevelopment')->willReturn($environmentName === 'development');
		$security = new WebSecurity();
		$stores = new Stores($this->createStub(SpecialStore::class), StoreOptions::default(), $security);
		if ($hasToken) {
			$stores->session->set($security->getCsrfKind(WebSecurity::CSRF_KIND_SESSION_KEY), 'test-token<&"');
		}
		$context = new HtmlNodeContext(
			new ProgramContext('', '', ''),
			$this->createStub(IUrlHelper::class),
			$stores,
			$environment,
			$security,
			new HtmlBuilder()
		);
		$parameter = new TemplateParameter(HttpStatus::OK, [
			'account_login_login_id' => 'login<&"',
			'account_login_password' => 'input<&"',
		], [
			'account_login_login_id' => ['invalid <id>'],
			'account_login_password' => ['invalid <password>'],
		]);
		$page = new AccountLoginPage(new RootProps($parameter), $context);
		$layout = $this->callInstanceMethod($page, 'getNode');
		$this->assertInstanceOf(DefaultPage::class, $layout);
		$this->assertSame($parameter, $layout->props->parameter);
		$this->assertSame('ログイン', $layout->props->title);
		$this->assertNotNull($layout->props->children);
		$html = $layout->props->children->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, ''));
		$dom = new DOMDocument();
		$this->assertTrue($dom->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>'));
		$xpath = new DOMXPath($dom);
		$formCount = $environmentName === 'development' ? 3 : 1;
		$this->assertSame($formCount, $xpath->query('//form')->length);
		$this->assertSame($formCount, $xpath->query('//form[@action="/account/login2"][@method="post"]')->length);
		$tokens = $xpath->query('//input[@name="' . $security->getCsrfKind(WebSecurity::CSRF_KIND_REQUEST_NAME) . '"]/@value');
		$this->assertSame($hasToken ? $formCount : 0, $tokens->length);
		foreach ($tokens as $token) {
			$this->assertSame('test-token<&"', $token->nodeValue);
		}
		foreach (['login_id' => 'text', 'password' => 'password'] as $field => $type) {
			$key = 'account_login_' . $field;
			$input = '//form[@class="page-account-login"]//input[@id="' . $key . '"]';
			$this->assertSame(1, $xpath->query($input . '[@name="' . $key . '"][@type="' . $type . '"][@required][@class="edit error"]')->length);
			$this->assertSame($parameter->values[$key], $xpath->evaluate('string(' . $input . '/@value)'));
		}
		$this->assertSame(1, $xpath->query('//input[@id="account_login_login_id"][@autofocus]')->length);
		$this->assertSame(2, $xpath->query('//ul[@class="errors value-error"]/li[@class="error"]')->length);
		$this->assertSame('invalid <id>', $xpath->evaluate('string((//ul[@class="errors value-error"]/li)[1])'));
		$this->assertSame(1, $xpath->query('//dt[@class="action"]')->length);
		$this->assertSame(1, $xpath->query('//a[@href="/password/reminder"]')->length);
		$this->assertSame($environmentName === 'development' ? 2 : 0, $xpath->query('//button[@class="link"][@data-dialog="disabled"]')->length);
	}
}
