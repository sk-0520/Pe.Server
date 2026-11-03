<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\ApiKeysEntityDao;
use PeServer\App\Models\Dao\Entities\PluginCategoriesEntityDao;
use PeServer\App\Models\Dao\Entities\PluginCategoryMappingsEntityDao;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuditLogsEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;
use PeServer\App\Models\Dao\Entities\UserChangeWaitEmailsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Domain\PluginState;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\Core\Cryptography;
use PeServer\Core\DI\IDiRegisterContainer;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mail\EmailMessage;
use PeServer\Core\Mail\Mailer;
use PeServer\Core\Web\WebSecurity;
use PeServerTest\ItBody;
use PeServerTest\ItControllerClass;
use PeServerTest\ItMockStores;
use PeServerTest\ItOptions;
use PeServerTest\ItSetup;
use PeServerTest\ItUseDatabaseTrait;
use PHPUnit\Framework\Attributes\DataProvider;

class AccountFamilyControllerTest extends ItControllerClass
{
	use ItUseDatabaseTrait;

	private const PLUGIN_ID = '12345678-1234-4234-8234-123456789abc';

	private function seed(ItSetup $setup): void
	{
		$crypto = $setup->container->get(AppCryptography::class);
		(new UsersEntityDao($setup->databaseContext))->insertUser(
			ItMockStores::SESSION_ACCOUNT_USER_ID,
            ItMockStores::SESSION_ACCOUNT_LOGIN_ID,
			UserLevel::USER,
            UserState::ENABLED,
            'Test <User>',
			$crypto->encrypt('old@example.com'),
            $crypto->toMark('old@example.com'),
			'https://example.com',
            '**description**',
            ''
		);
		(new UserAuthenticationsEntityDao($setup->databaseContext))->insertUserAuthentication(
			ItMockStores::SESSION_ACCOUNT_USER_ID,
            Cryptography::hashPassword('password123')
		);
		(new PluginCategoriesEntityDao($setup->databaseContext))->insertPluginCategory('test-category', 'Category <one>', '');
	}

	private function options(array $body = []): ItOptions
	{
		return new ItOptions(stores: ItMockStores::account(UserLevel::USER), body: ItBody::form($body));
	}

	public static function provider_get(): array
	{
		return [
			['/account', 'ユーザー情報', null],
			['/account/user', 'ユーザー情報', null],
			['/account/user/api', 'APIキー登録', '/account/user/api'],
			['/account/user/password', 'パスワード変更', '/account/user/password'],
			['/account/user/email', 'メールアドレス変更', '/account/user/email'],
			['/account/user/plugin', 'プラグイン 登録', '/account/user/plugin'],
			['/account/user/plugin/reserve', 'プラグイン 予約', '/account/user/plugin/reserve'],
			['/account/user/audit-logs', '監査ログ', null],
			['/account/user/audit-logs/page/2', '監査ログ', null],
		];
	}

	#[DataProvider('provider_get')]
	public function test_get(string $path, string $title, ?string $action): void
	{
		$actual = $this->call(HttpMethod::Get, $path, $this->options(), $this->seed(...));
		$this->assertStatusOK($actual);
		$this->assertTitle($title, $actual);
		$this->assertCount(0, $actual->html->path()->collections("//*[@id='node-template-indicator']"));
		$this->assertCount(1, $actual->html->path()->collections("//header//a[@href='/account/user']"));
		$this->assertCount(1, $actual->html->path()->collections("//header//a[@href='/account/logout']"));
		if ($action !== null) {
			$this->assertCount(1, $actual->html->path()->collections("//form[@action='$action'][@method='post']"));
			$csrfName = $actual->container->get(WebSecurity::class)->getCsrfKind(WebSecurity::CSRF_KIND_REQUEST_NAME);
			$this->assertCount(1, $actual->html->path()->collections("//form//input[@name='$csrfName'][@type='hidden']"));
		}
		if (str_contains($path, '/plugin')) {
			$this->assertCount(1, $actual->html->path()->collections("//input[@id='pg-plugin-id'][not(@readonly)]"));
			$this->assertCount(1, $actual->html->path()->collections("//button[@id='pg-plugin-id-auto-generate'][@type='button']"));
			$this->assertCount(1, $actual->html->path()->collections("//script[contains(@src,'/scripts/plugin_edit.js')]"));
		}
	}

	public function test_index_anonymous_and_logout(): void
	{
		$actual = $this->call(HttpMethod::Get, '/account');
		$this->assertStatusOK($actual);
		$this->assertTitle('ログイン', $actual);
		$this->assertCount(1, $actual->html->path()->collections("//header//a[@href='/account/signup']"));
		$actual = $this->call(HttpMethod::Get, '/account/logout', $this->options(), $this->seed(...));
		$this->assertRedirectPath(HttpStatus::Found, '', null, $actual);
		$this->assertSame(AuditLog::LOGOUT, $this->getMaybeLatestAuditLog($actual->openDB())->fields['event']);
	}

	public function test_user_plugins_and_links(): void
	{
		$actual = $this->call(HttpMethod::Get, '/account/user', $this->options(), function (ItSetup $setup): void {
			$this->seed($setup);
			(new PluginsEntityDao($setup->databaseContext))->insertPlugin(
				self::PLUGIN_ID,
                ItMockStores::SESSION_ACCOUNT_USER_ID,
                'test-plugin',
                'Plugin <name>',
				PluginState::ENABLED,
                '',
                ''
			);
		});
		$this->assertCount(1, $actual->html->path()->collections("//a[@href='/account/user/plugin/" . self::PLUGIN_ID . "'][@title='Plugin <name>']"));
		foreach (['edit', 'api', 'email', 'password', 'plugin', 'plugin/reserve', 'audit-logs', 'audit-logs/download'] as $tail) {
			$this->assertCount(1, $actual->html->path()->collections("//*[@id='content']//a[@href='/account/user/$tail']"));
		}
		$this->assertTextNode('Test <User>', $actual->html->path()->collections("//dt[text()='名前']/following-sibling::dd[1]")->single());
	}

	public function test_api_cycle(): void
	{
		$created = $this->call(HttpMethod::Post, '/account/user/api', $this->options(), $this->seed(...));
		$this->assertRedirectPath(HttpStatus::Found, 'account/user/api', null, $created);
		$key = (new ApiKeysEntityDao($created->openDB()))->selectApiKeyByUserId(ItMockStores::SESSION_ACCOUNT_USER_ID)->fields;
		$shown = $this->call(HttpMethod::Get, '/account/user/api', $this->options(), null, $created);
		$this->assertTitle('APIキー確認', $shown);
		$this->assertTextNode($key['secret_key'], $shown->html->path()->collections("//tbody/tr[3]/td/pre")->single());
		$again = $this->call(HttpMethod::Get, '/account/user/api', $this->options(), null, $shown);
		$this->assertCount(0, $again->html->path()->collections("//tbody/tr[3]"));
		$deleted = $this->call(HttpMethod::Post, '/account/user/api', $this->options(), null, $again);
		$this->assertRedirectPath(HttpStatus::Found, 'account/user/api', null, $deleted);
		$this->assertFalse((new ApiKeysEntityDao($deleted->openDB()))->selectExistsApiKeyByUserId(ItMockStores::SESSION_ACCOUNT_USER_ID));
	}

	public static function provider_routes(): array
	{
		return [['/account', '']];
	}

	#[DataProvider('provider_routes')]
	public function test_password_failure_and_success(string $base, string $suffix): void
	{
		$bad = $this->call(HttpMethod::Post, "$base/user/password$suffix", $this->options([
			'account_password_current' => 'wrong',
			'account_password_new' => 'x',
			'account_password_confirm' => 'different',
		]), $this->seed(...));
		$this->assertStatusOK($bad);
		$this->assertVisibleCommonError([], $bad);
		$this->assertCount(1, $bad->html->path()->collections("//form[@action='/account/user/password']"));
		$this->assertCount(3, $bad->html->path()->collections("//input[@type='password'][@value=''][contains(@class,'error')]/following-sibling::ul"));
		$good = $this->call(HttpMethod::Post, "$base/user/password$suffix", $this->options([
			'account_password_current' => 'password123',
			'account_password_new' => 'newpassword123',
			'account_password_confirm' => 'newpassword123',
		]), null, $bad);
		$this->assertRedirectPath(HttpStatus::Found, 'account/user', null, $good);
		$password = (new UserAuthenticationsEntityDao($good->openDB()))->selectPassword(ItMockStores::SESSION_ACCOUNT_USER_ID)->fields['current_password'];
		$this->assertTrue(Cryptography::verifyPassword('newpassword123', $password));
		$this->assertSame(AuditLog::USER_PASSWORD_CHANGE, $this->getMaybeLatestAuditLog($good->openDB())->fields['event']);
		$user = $this->call(HttpMethod::Get, '/account/user', $this->options(), null, $good);
		$this->assertStatusOK($user);
		$this->assertCount(0, $user->html->path()->collections("//*[@id='node-template-indicator']"));
	}

	#[DataProvider('provider_routes')]
	public function test_email_request_and_invalid_token(string $base, string $suffix): void
	{
		$mailer = $this->createMock(Mailer::class);
		$mailer->expects($this->once())->method('send');
		$mailer->expects($this->once())->method('setMessage')->with($this->callback(fn(EmailMessage $message) => $message->hasHtml()));
		$sent = $this->call(HttpMethod::Post, "$base/user/email$suffix", $this->options([
			'account_email_mode' => 'edit', 'account_email_email' => 'new@example.com',
		]), function (ItSetup $setup) use ($mailer): void {
			$this->seed($setup);
			$this->assertInstanceOf(IDiRegisterContainer::class, $setup->container);
			$setup->container->registerValue($mailer, Mailer::class);
		});
		$this->assertRedirectPath(HttpStatus::Found, 'account/user/email', null, $sent);
		$waiting = $this->call(HttpMethod::Get, "$base/user/email$suffix", $this->options(), null, $sent);
		$this->assertCount(2, $waiting->html->path()->collections("//form[@action='/account/user/email']"));
		$this->assertTextNode('new@example.com', $waiting->html->path()->collections("//section[@class='page-account-email-confirm']//code")->single());
		$time = $waiting->html->path()->collections("//section[@class='page-account-email-confirm']//time")->single();
		$this->assertSame($time->raw->textContent, $time->getAttribute('datetime'));
		$bad = $this->call(HttpMethod::Post, "$base/user/email$suffix", $this->options([
			'account_email_mode' => 'confirm', 'account_email_token' => 'bad-token',
		]), null, $waiting);
		$this->assertVisibleCommonError([], $bad);
		$this->assertCount(1, $bad->html->path()->collections("//input[@name='account_email_token'][@value=''][contains(@class,'error')]"));
	}

	#[DataProvider('provider_routes')]
	public function test_email_confirm(string $base, string $suffix): void
	{
		$mailer = $this->createMock(Mailer::class);
		$mailer->expects($this->exactly(2))->method('send');
		$mailer->expects($this->exactly(2))->method('setMessage')->with($this->callback(fn(EmailMessage $message) => $message->hasHtml()));
		$confirmed = $this->call(HttpMethod::Post, "$base/user/email$suffix", $this->options([
			'account_email_mode' => 'confirm', 'account_email_token' => '12345678',
		]), function (ItSetup $setup) use ($mailer): void {
			$this->seed($setup);
			$crypto = $setup->container->get(AppCryptography::class);
			(new UserChangeWaitEmailsEntityDao($setup->databaseContext))->insertWaitEmails(
				ItMockStores::SESSION_ACCOUNT_USER_ID,
                $crypto->encrypt('new@example.com'),
				$crypto->toMark('new@example.com'),
                '12345678'
			);
			$this->assertInstanceOf(IDiRegisterContainer::class, $setup->container);
			$setup->container->registerValue($mailer, Mailer::class);
		});
		$this->assertRedirectPath(HttpStatus::Found, 'account/user', null, $confirmed);
		$this->assertSame(0, $confirmed->openDB()->selectSingleCount('select count(*) from user_change_wait_emails'));
		$email = $confirmed->openDB()->querySingle('select email from users where user_id = :user_id', ['user_id' => ItMockStores::SESSION_ACCOUNT_USER_ID])->fields['email'];
		$this->assertSame('new@example.com', $confirmed->container->get(AppCryptography::class)->decrypt($email));
	}

	public static function provider_invalid_forms(): array
	{
		return [
			['/account/user/email', ['account_email_mode' => 'edit', 'account_email_email' => 'invalid'], 'account_email_email'],
			['/account/user/plugin', ['account_plugin_plugin_id' => 'invalid'], 'account_plugin_plugin_id'],
			['/account/user/plugin/reserve', ['account_plugin_reserve_plugin_id' => 'invalid'], 'account_plugin_reserve_plugin_id'],
		];
	}

	#[DataProvider('provider_invalid_forms')]
	public function test_invalid_forms(string $path, array $body, string $field): void
	{
		$actual = $this->call(HttpMethod::Post, $path, $this->options($body), $this->seed(...));
		$this->assertStatusOK($actual);
		$this->assertVisibleCommonError([], $actual);
		$this->assertCount(1, $actual->html->path()->collections("//form[@action='$path']//input[@name='$field'][contains(@class,'error')]/following-sibling::ul"));
		$this->assertValue('invalid', $actual->html->path()->collections("//input[@name='$field']")->single());
	}

	#[DataProvider('provider_routes')]
	public function test_plugin_reserve(string $base, string $suffix): void
	{
		$actual = $this->call(HttpMethod::Post, "$base/user/plugin/reserve$suffix", $this->options([
			'account_plugin_reserve_plugin_id' => self::PLUGIN_ID,
			'account_plugin_reserve_plugin_name' => 'test-plugin',
		]), $this->seed(...));
		$this->assertRedirectPath(HttpStatus::Found, 'account/user/plugin/' . self::PLUGIN_ID, null, $actual);
		$this->assertSame(PluginState::RESERVED, (new PluginsEntityDao($actual->openDB()))->selectEditPlugin(self::PLUGIN_ID)->fields['state']);
		$edit = $this->call(HttpMethod::Get, "$base/user/plugin$suffix/" . self::PLUGIN_ID, $this->options(), null, $actual);
		$this->assertTitle('プラグイン 編集: test-plugin', $edit);
		$this->assertCount(1, $edit->html->path()->collections("//form[@action='/account/user/plugin/" . self::PLUGIN_ID . "']"));
		$this->assertCount(0, $edit->html->path()->collections("//*[@id='node-template-indicator']"));
		$this->assertCount(2, $edit->html->path()->collections("//form//input[@readonly]"));
		$this->assertCount(1, $edit->html->path()->collections("//input[@id='pg-plugin-id'][@name='account_plugin_plugin_id']"));
		$this->assertCount(1, $edit->html->path()->collections("//input[@name='account_plugin_state'][@value='reserved'][@disabled][@checked]"));
	}

	private function pluginBody(): array
	{
		return [
			'account_plugin_plugin_id' => self::PLUGIN_ID,
			'account_plugin_plugin_name' => 'test-plugin',
			'account_plugin_display_name' => 'Test <Plugin>',
			'account_plugin_state' => PluginState::DISABLED,
			'account_plugin_check_url' => 'https://example.com/check',
			'account_plugin_lp_url' => 'https://example.com',
			'account_plugin_project_url' => '',
			'account_plugin_description' => '**preview**',
			'plugin_category_test-category' => 'on',
		];
	}

	#[DataProvider('provider_routes')]
	public function test_plugin_failure_retains_values(string $base, string $suffix): void
	{
		$body = $this->pluginBody();
		$body['account_plugin_check_url'] = 'invalid-url';
		$actual = $this->call(HttpMethod::Post, "$base/user/plugin$suffix", $this->options($body), $this->seed(...));
		$this->assertStatusOK($actual);
		$this->assertVisibleCommonError([], $actual);
		$this->assertValue('invalid-url', $actual->html->path()->collections("//input[@name='account_plugin_check_url'][contains(@class,'error')]")->single());
		$this->assertCount(1, $actual->html->path()->collections("//input[@name='plugin_category_test-category'][@checked]"));
		$this->assertCount(1, $actual->html->path()->collections("//input[@name='account_plugin_state'][@value='disabled'][@checked]"));
		$this->assertCount(1, $actual->html->path()->collections("//button[@id='pg-plugin-id-auto-generate'][@type='button']"));
		$this->assertCount(1, $actual->html->path()->collections("//script[contains(@src,'/scripts/plugin_edit.js')]"));
		$this->assertTextNode('**preview**', $actual->html->path()->collections("//textarea[@data-markdown-result='.markdown-browser']")->single());
		$this->assertTextNode('preview', $actual->html->path()->collections("//section[@class='markdown markdown-browser']//strong")->single());
	}

	#[DataProvider('provider_routes')]
	public function test_plugin_register_and_update(string $base, string $suffix): void
	{
		$created = $this->call(HttpMethod::Post, "$base/user/plugin$suffix", $this->options($this->pluginBody()), $this->seed(...));
		$this->assertRedirectPath(HttpStatus::Found, 'account/user/plugin/' . self::PLUGIN_ID, null, $created);
		$edit = $this->call(HttpMethod::Get, "$base/user/plugin$suffix/" . self::PLUGIN_ID, $this->options(), null, $created);
		$this->assertCount(1, $edit->html->path()->collections("//input[@name='plugin_category_test-category'][@checked]"));
		$this->assertTextNode('preview', $edit->html->path()->collections("//section[@class='markdown markdown-browser']//strong")->single());
		$body = $this->pluginBody();
		$body['from_account_plugin_plugin_id'] = self::PLUGIN_ID;
		$body['account_plugin_display_name'] = 'Updated';
		unset($body['plugin_category_test-category']);
		$updated = $this->call(HttpMethod::Post, "$base/user/plugin$suffix/" . self::PLUGIN_ID, $this->options($body), null, $edit);
		$this->assertRedirectPath(HttpStatus::Found, 'account/user/plugin/' . self::PLUGIN_ID, null, $updated);
		$this->assertSame('Updated', (new PluginsEntityDao($updated->openDB()))->selectEditPlugin(self::PLUGIN_ID)->fields['display_name']);
		$this->assertSame([], (new PluginCategoryMappingsEntityDao($updated->openDB()))->selectPluginCategoriesByPluginId(self::PLUGIN_ID));
	}

	#[DataProvider('provider_routes')]
	public function test_audit_paging_and_download(string $base, string $suffix): void
	{
		$first = $this->call(HttpMethod::Get, "$base/user/audit-logs$suffix", $this->options(), function (ItSetup $setup): void {
			$this->seed($setup);
			$dao = new UserAuditLogsEntityDao($setup->databaseContext);
			for ($i = 0; $i < 12; $i++) {
				$dao->insertLog(ItMockStores::SESSION_ACCOUNT_USER_ID, 'event-' . $i, '{"value":"<test>"}', '127.0.0.1', 'Test UA');
			}
		});
		$this->assertCount(10, $first->html->path()->collections("//table/tbody/tr"));
		$this->assertCount(1, $first->html->path()->collections("//ul[@class='pagination']//a[@href='/account/user/audit-logs/page/2'][text()='2']"));
		$this->assertCount(10, $first->html->path()->collections("//td[@data-clipboard='block']/pre/code"));
		$second = $this->call(HttpMethod::Get, "$base/user/audit-logs$suffix/page/2", $this->options(), null, $first);
		$this->assertCount(2, $second->html->path()->collections("//table/tbody/tr"));
		$download = $this->call(HttpMethod::Get, '/account/user/audit-logs/download', $this->options(), null, $second);
		$this->assertStatusOK($download);
		$this->assertCount(12, $download->json);
		$legacy = $this->call(HttpMethod::Get, '/account/user/audit-logs/download', $this->options(), null, $second);
		$this->assertSame($legacy->json, $download->json);
	}
}
