<?php

declare(strict_types=1);

namespace PeServerUT\App\Views\Pages\Account;

use DOMXPath;
use PeServer\App\Views\Components\Pages\Default\DefaultPage;
use PeServer\App\Views\Pages\Account\AccountUserEditPage;
use PeServer\Core\Environment;
use PeServer\Core\Html\HtmlDocument;
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

class AccountUserEditPageTest extends TestClass
{
	public static function provider_page(): array
	{
		return [[false, false], [true, false], [true, true]];
	}

	#[DataProvider('provider_page')]
	public function test_page(bool $hasToken, bool $hasError): void
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
		$parameter = new TemplateParameter(HttpStatus::OK, [
			'account_edit_name' => 'name<&"',
			'account_edit_website' => 'https://example.com/?q=<&"',
			'account_edit_description' => "**bold**\n\n<&\"</textarea>",
		], $hasError ? [
			'account_edit_name' => ['invalid name'],
			'account_edit_website' => ['invalid website'],
			'account_edit_description' => ['invalid description'],
		] : []);
		$page = new AccountUserEditPage(new RootProps($parameter), $context);
		$layout = $this->callInstanceMethod($page, 'getNode');
		$this->assertInstanceOf(DefaultPage::class, $layout);
		$this->assertSame($parameter, $layout->props->parameter);
		$this->assertSame('ユーザー情報 編集', $layout->props->title);
		$this->assertSame('/scripts/user_edit.js', $layout->props->defaultScriptPath);
		$html = $layout->props->children->build(0, new BuildOptions(BuildOptions::FORMAT_NONE, ''));
		$dom = new HtmlDocument();
		$this->assertTrue($dom->raw->loadHTML('<?xml encoding="UTF-8"><html><body>' . $html . '</body></html>'));
		$xpath = new DOMXPath($dom->raw);
		$this->assertSame(1, $xpath->query('//form[@action="/account/user/edit2"][@method="post"][@class="page-account-user"]')->length);
		$this->assertSame(1, $xpath->query('//input[@id="account_edit_name"][@type="text"][@required]')->length);
		$this->assertSame(1, $xpath->query('//input[@id="account_edit_website"][@type="url"]')->length);
		$this->assertSame($parameter->values['account_edit_name'], $xpath->evaluate('string(//input[@name="account_edit_name"]/@value)'));
		$this->assertSame($parameter->values['account_edit_website'], $xpath->evaluate('string(//input[@name="account_edit_website"]/@value)'));
		$this->assertSame($parameter->values['account_edit_description'], $xpath->evaluate('string(//textarea[@name="account_edit_description"])'));
		$this->assertSame('.markdown-browser', $xpath->evaluate('string(//textarea/@data-markdown-result)'));
		$this->assertSame($hasError ? 'edit markdown-editor error' : 'edit markdown-editor', $xpath->evaluate('string(//textarea/@class)'));
		$this->assertSame($hasError ? 3 : 0, $xpath->query('//ul[@class="errors value-error"]/li')->length);
		$this->assertSame(2, $xpath->query('//input[@type="radio"][@name="tab_markdown"][@class="tab_check"]')->length);
		$this->assertSame(1, $xpath->query('//input[@id="description_markdown_source"][@checked]/following-sibling::label[1][@for="description_markdown_source"]')->length);
		$this->assertSame(1, $xpath->query('//input[@id="description_markdown_preview"][not(@checked)]/following-sibling::label[1][@for="description_markdown_preview"]')->length);
		$this->assertSame(2, $xpath->query('//div[@class="tab"]/div[@class="tab_content"]')->length);
		$this->assertSame('bold', $xpath->evaluate('string(//section[@class="markdown markdown-browser"]/p/strong)'));
		$tokens = $xpath->query('//input[@name="' . $security->getCsrfKind(WebSecurity::CSRF_KIND_REQUEST_NAME) . '"]/@value');
		$this->assertSame($hasToken ? 1 : 0, $tokens->length);
		if ($hasToken) {
			$this->assertSame('token<&"', $tokens->item(0)->nodeValue);
		}
	}
}
