<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\ApiKeysEntityDao;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Domain\AccountValidator;
use PeServer\App\Models\Domain\PluginState;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\Core\Cryptography;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Text;
use PeServer\Core\Html\HtmlTagElement;
use PeServer\Core\Throws\HttpStatusException;
use PeServerTest\ItBody;
use PeServerTest\ItControllerClass;
use PeServerTest\ItLoginTrait;
use PeServerTest\ItMockStores;
use PeServerTest\ItOptions;
use PeServerTest\ItSetup;
use PeServerTest\ItUseDatabaseTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Throwable;

class AccountControllerTest extends ItControllerClass
{
	use ItUseDatabaseTrait;
	use ItLoginTrait;

	public static function provider_it_notLogin()
	{
		return self::_provider_it_notLogin([
			'/account',
			'/account/login',
		]);
	}

	#[DataProvider('provider_it_notLogin')]
	public function test_it_notLogin(string $path)
	{
		$this->_test_notLogin($path);
	}

	public function test_login_get_notLogin()
	{
		$actual = $this->call(HttpMethod::Get, '/account/login');

		$this->assertStatusOK($actual);
		$this->assertTitle('ログイン', $actual);

		$this->assertTextNode(
			'',
			$actual->html->path()->collections(
				"//*[@id='content']/form[1][@action='/account/login']//*[contains(@class,'input')]//dt[text()='ログインID']/following-sibling::dd[1]"
			)->single()
		);

		$this->assertTextNode(
			'',
			$actual->html->path()->collections(
				"//*[@id='content']/form[1][@action='/account/login']//*[contains(@class,'input')]//dt[text()='パスワード']/following-sibling::dd[1]"
			)->single()
		);
	}

	public function test_login_get_login()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);

		$actual = $this->call(HttpMethod::Get, '/account/login', $options);
		$this->assertRedirectPath(HttpStatus::Found, '/account/user', null, $actual);
	}

	public function test_login_get2_notLogin()
	{
		$actual = $this->call(HttpMethod::Get, '/account/login2');

		$this->assertStatusOK($actual);
		$this->assertTitle('ログイン', $actual);
		$this->assertCount(1, $actual->html->path()->collections(
			"//*[@id='content']/form[@action='/account/login2']//input[@name='account_login_login_id']"
		));
	}

	public function test_login_get2_login()
	{
		$options = new ItOptions(stores: ItMockStores::account(UserLevel::USER));
		$actual = $this->call(HttpMethod::Get, '/account/login2', $options);

		$this->assertRedirectPath(HttpStatus::Found, '/account/user', null, $actual);
	}

	public static function provider_login_post_notLogin_throw()
	{
		return [
			[ItMockStores::none()],
			[ItMockStores::anonymous(false, false, false, false, false)],
			[ItMockStores::anonymous(false, true, false, false, false)],
			[ItMockStores::anonymous(false, false, true, false, false)],
			[ItMockStores::anonymous(false, false, false, true, false)],
			[ItMockStores::anonymous(false, false, false, false, true)],
		];
	}

	#[DataProvider('provider_login_post_notLogin_throw')]
	public function test_login_post_notLogin_throw(ItMockStores $input)
	{
		$options = new ItOptions(
			stores: $input,
		);
		$this->expectException(HttpStatusException::class);
		$this->call(HttpMethod::Post, '/account/login', $options);
	}

	public static function provider_login_post_empty_input()
	{
		return [
			['', ''],
			['login-id', ''],
			['', 'password'],
		];
	}

	#[DataProvider('provider_login_post_empty_input')]
	public function test_login_post_empty_input(string $loginId, string $password)
	{
		$options = new ItOptions(
			stores: ItMockStores::anonymous(login: true),
			body: ItBody::form([
				'account_login_login_id' => $loginId,
				'account_login_password' => $password,
			]),
		);
		$actual = $this->call(HttpMethod::Post, '/account/login', $options);

		$this->assertStatusOK($actual);
		$this->assertTitle('ログイン', $actual);

		$this->assertVisibleCommonError(['ログインID/パスワードが正しくありません'], $actual);

		$this->assertTextNode(
			'',
			$actual->html->path()->collections(
				"//*[@id='content']/form[1][@action='/account/login']//*[contains(@class,'input')]//dt[text()='ログインID']/following-sibling::dd[1]"
			)->single()
		);

		$this->assertTextNode(
			'',
			$actual->html->path()->collections(
				"//*[@id='content']/form[1][@action='/account/login']//*[contains(@class,'input')]//dt[text()='パスワード']/following-sibling::dd[1]"
			)->single()
		);
	}

	#[DataProvider('provider_login_paths')]
	public function test_login_post_failure(string $path)
	{
		$options = new ItOptions(
			stores: ItMockStores::anonymous(login: true),
			body: ItBody::form([
				'account_login_login_id' => ItMockStores::SESSION_ACCOUNT_LOGIN_ID,
				'account_login_password' => 'password',
			]),
		);
		$actual = $this->call(HttpMethod::Post, $path, $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
			$userAuthenticationsEntityDao->insertUserAuthentication(ItMockStores::SESSION_ACCOUNT_USER_ID, Cryptography::hashPassword('@'));
		});

		$this->assertStatusOK($actual);
		$this->assertTitle('ログイン', $actual);

		$this->assertVisibleCommonError(['ログインID/パスワードが正しくありません'], $actual);

		$this->assertTextNode(
			'',
			$actual->html->path()->collections(
				"//*[@id='content']/form[1][@action='$path']//*[contains(@class,'input')]//dt[text()='ログインID']/following-sibling::dd[1]"
			)->single()
		);

		$this->assertTextNode(
			'',
			$actual->html->path()->collections(
				"//*[@id='content']/form[1][@action='$path']//*[contains(@class,'input')]//dt[text()='パスワード']/following-sibling::dd[1]"
			)->single()
		);

		$context = $actual->openDB();
		$auditResult = $this->getMaybeLatestAuditLog($context);
		$this->assertSame(ItMockStores::SESSION_ACCOUNT_USER_ID, $auditResult->fields['user_id']);
		$this->assertSame(AuditLog::LOGIN_FAILED, $auditResult->fields['event']);
	}

	public function test_login_post_failure_enabled_setup_user()
	{
		$options = new ItOptions(
			stores: ItMockStores::anonymous(login: true),
			body: ItBody::form([
				'account_login_login_id' => ItMockStores::SESSION_ACCOUNT_LOGIN_ID,
				'account_login_password' => 'password',
			]),
		);
		$actual = $this->call(HttpMethod::Post, '/account/login', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
			$userAuthenticationsEntityDao->insertUserAuthentication(ItMockStores::SESSION_ACCOUNT_USER_ID, Cryptography::hashPassword('@'));
		});

		$this->assertStatusOK($actual);
		$this->assertTitle('ログイン', $actual);

		$this->assertVisibleCommonError(['ログインID/パスワードが正しくありません'], $actual);

		$this->assertTextNode(
			'',
			$actual->html->path()->collections(
				"//*[@id='content']/form[1][@action='/account/login']//*[contains(@class,'input')]//dt[text()='ログインID']/following-sibling::dd[1]"
			)->single()
		);

		$this->assertTextNode(
			'',
			$actual->html->path()->collections(
				"//*[@id='content']/form[1][@action='/account/login']//*[contains(@class,'input')]//dt[text()='パスワード']/following-sibling::dd[1]"
			)->single()
		);
	}

	public static function provider_login_paths(): array
	{
		return [
			['/account/login'],
			['/account/login2'],
		];
	}

	#[DataProvider('provider_login_paths')]
	public function test_login_post_success(string $path)
	{
		$options = new ItOptions(
			stores: ItMockStores::anonymous(login: true),
			body: ItBody::form([
				'account_login_login_id' => ItMockStores::SESSION_ACCOUNT_LOGIN_ID,
				'account_login_password' => 'password',
			]),
		);
		$actual = $this->call(HttpMethod::Post, $path, $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
			$userAuthenticationsEntityDao->insertUserAuthentication(ItMockStores::SESSION_ACCOUNT_USER_ID, Cryptography::hashPassword('password'));
		});

		$this->assertRedirectPath(HttpStatus::Found, '/account', null, $actual);

		$context = $actual->openDB();
		$auditResult = $this->getMaybeLatestAuditLog($context);
		$this->assertSame(ItMockStores::SESSION_ACCOUNT_USER_ID, $auditResult->fields['user_id']);
		$this->assertSame(AuditLog::LOGIN_SUCCESS, $auditResult->fields['event']);
	}

	#[DataProvider('provider_login_paths')]
	public function test_login_post_login(string $path)
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Post, $path, $options);
		$this->assertRedirectPath(HttpStatus::Found, '/account/user', null, $actual);
	}

	#[DataProvider('provider_login_post_empty_input')]
	public function test_login_post2_empty_input(string $loginId, string $password)
	{
		$options = new ItOptions(
			stores: ItMockStores::anonymous(login: true),
			body: ItBody::form([
				'account_login_login_id' => $loginId,
				'account_login_password' => $password,
			]),
		);
		$actual = $this->call(HttpMethod::Post, '/account/login2', $options);

		$this->assertStatusOK($actual);
		$this->assertTitle('ログイン', $actual);
		$this->assertVisibleCommonError(['ログインID/パスワードが正しくありません'], $actual);
		$this->assertCount(1, $actual->html->path()->collections(
			"//*[@id='content']/form[@action='/account/login2']//input[@name='account_login_login_id']"
		));
	}

	#[DataProvider('provider_login_post_notLogin_throw')]
	public function test_login_post2_notLogin_throw(ItMockStores $input)
	{
		$this->expectException(HttpStatusException::class);
		$this->call(HttpMethod::Post, '/account/login2', new ItOptions(stores: $input));
	}

	public function test_logout_notLogin()
	{
		$actual = $this->call(HttpMethod::Get, '/account/logout');
		$this->assertRedirectPath(HttpStatus::Found, ''/* リダイレクトは / を返してるけどまぁ */, null, $actual);

		$context = $actual->openDB();
		$this->assertSame(0, $context->selectSingleCount('select count(*) from user_audit_logs'));
	}

	public function test_logout_login()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Get, '/account/logout', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
			$userAuthenticationsEntityDao->insertUserAuthentication(ItMockStores::SESSION_ACCOUNT_USER_ID, Cryptography::hashPassword('@'));
		});
		$this->assertRedirectPath(HttpStatus::Found, '', null, $actual);

		$context = $actual->openDB();
		$auditResult = $this->getMaybeLatestAuditLog($context);
		$this->assertSame(ItMockStores::SESSION_ACCOUNT_USER_ID, $auditResult->fields['user_id']);
		$this->assertSame(AuditLog::LOGOUT, $auditResult->fields['event']);
	}

	public function test_user()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Get, '/account', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
		});

		$this->assertStatusOk($actual);
		$this->assertTitle('ユーザー情報', $actual);

		$this->assertTextNode(
			ItMockStores::SESSION_ACCOUNT_USER_ID,
			$actual->html->path()->collections(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), 'ユーザーID')]/following-sibling::dd[1]//*[@data-role='value']"
			)->single()
		);

		$this->assertTextNode(
			ItMockStores::SESSION_ACCOUNT_LOGIN_ID,
			$actual->html->path()->collections(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), 'ログインID')]/following-sibling::dd[1]//*[@data-role='value']"
			)->single()
		);

		$this->assertTextNode(
			UserLevel::toString(UserLevel::USER),
			$actual->html->path()->collections(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), '権限')]/following-sibling::dd[1][@data-role='value']"
			)->single()
		);

		$this->assertTextNode(
			ItMockStores::SESSION_ACCOUNT_NAME,
			$actual->html->path()->collections(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), '名前')]/following-sibling::dd[1][@data-role='value']"
			)->single()
		);

		$this->assertTextNode(
			ItMockStores::SESSION_ACCOUNT_WEBSITE,
			$actual->html->path()->collections(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), 'Webサイト')]/following-sibling::dd[1][@data-role='value']"
			)->single()
		);

		$this->assertTextNode(
			'未登録',
			$actual->html->path()->collections(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), 'プラグイン')]/following-sibling::dd[1][@data-role='value']"
			)->single()
		);
	}

	public function test_user_not_register_website()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Get, '/account', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, 'email', 0, '', 'd', 'n');
		});

		$this->assertStatusOk($actual);

		$this->assertTextNode(
			'未登録',
			$actual->html->path()->collections(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), 'Webサイト')]/following-sibling::dd[1][@data-role='value']"
			)->single()
		);
	}

	public function test_user_plugins()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Get, '/account', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);
			$pluginsEntityDao = new PluginsEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID . '-OTHER', ItMockStores::SESSION_ACCOUNT_LOGIN_ID . '-OTHER', UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);

			$pluginsEntityDao->insertPlugin('A0', ItMockStores::SESSION_ACCOUNT_USER_ID . '-OTHER', 'PLUGIN-A0', 'plugin-a0', PluginState::ENABLED, 'P-A0-D', '');
			$pluginsEntityDao->insertPlugin('A2', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-A2', 'plugin-a2', PluginState::ENABLED, 'P-A-D2', '');
			$pluginsEntityDao->insertPlugin('A1', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-A1', 'plugin-a1', PluginState::ENABLED, 'P-A-D1', '');
			$pluginsEntityDao->insertPlugin('B0', ItMockStores::SESSION_ACCOUNT_USER_ID . '-OTHER', 'PLUGIN-B0', 'plugin-b0', PluginState::CHECK_FAILED, 'P-B0-D', '');
			$pluginsEntityDao->insertPlugin('B2', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-B2', 'plugin-b2', PluginState::CHECK_FAILED, 'P-B-D2', '');
			$pluginsEntityDao->insertPlugin('B1', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-B1', 'plugin-b1', PluginState::CHECK_FAILED, 'P-B-D1', '');
			$pluginsEntityDao->insertPlugin('C0', ItMockStores::SESSION_ACCOUNT_USER_ID . '-OTHER', 'PLUGIN-C0', 'plugin-c0', PluginState::DISABLED, 'P-C0-D', '');
			$pluginsEntityDao->insertPlugin('C2', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-C2', 'plugin-c2', PluginState::DISABLED, 'P-C-D2', '');
			$pluginsEntityDao->insertPlugin('C1', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-C1', 'plugin-c1', PluginState::DISABLED, 'P-C-D1', '');
			$pluginsEntityDao->insertPlugin('D0', ItMockStores::SESSION_ACCOUNT_USER_ID . '-OTHER', 'PLUGIN-D0', 'plugin-d0', PluginState::RESERVED, 'P-D0-D', '');
			$pluginsEntityDao->insertPlugin('D2', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-D2', 'plugin-d2', PluginState::RESERVED, 'P-D-D2', '');
			$pluginsEntityDao->insertPlugin('D1', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-D1', 'plugin-d1', PluginState::RESERVED, 'P-D-D1', '');
		});

		$this->assertStatusOk($actual);

		$expectedItems = [
			[
				'id' => 'A1',
				'name' => 'PLUGIN-A1',
			],
			[
				'id' => 'A2',
				'name' => 'PLUGIN-A2',
			],
			[
				'id' => 'B1',
				'name' => 'PLUGIN-B1',
			],
			[
				'id' => 'B2',
				'name' => 'PLUGIN-B2',
			],
			[
				'id' => 'D1',
				'name' => 'PLUGIN-D1',
			],
			[
				'id' => 'D2',
				'name' => 'PLUGIN-D2',
			],
			[
				'id' => 'C1',
				'name' => 'PLUGIN-C1',
			],
			[
				'id' => 'C2',
				'name' => 'PLUGIN-C2',
			],
		];

		$actual->html->save("x:\\a.html");

		$actualItems = $actual->html->path()->collections(
			"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), 'プラグイン')]/following-sibling::dd[1][@data-role='value']//tbody//tr/td/a"
		)->toArray();

		$this->assertCount(count($expectedItems), $actualItems);
		for ($i = 0; $i < count($expectedItems); $i++) {
			$expectedItem = $expectedItems[$i];
			/** @var HtmlTagElement */
			$actualItem = $actualItems[$i];

			$this->assertStringEndsWith($expectedItem['id'], $actualItem->getAttribute('href'));
			$this->assertTextNode($expectedItem['name'], $actualItem);
		}
	}

	public function test_user_edit_get_notLogin()
	{
		try {
			$this->call(HttpMethod::Get, '/account/user/edit');
			$this->fail();
		} catch (Throwable $ex) {
			// ミドルウェアで処理してるのでロジック的には例外で死んでる
			$this->success();
		}
	}

	public function test_user_edit_get()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Get, '/account/user/edit', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
		});

		$this->assertStatusOk($actual);

		$this->assertValue(
			ItMockStores::SESSION_ACCOUNT_NAME,
			$actual->html->path()->collections(
				"//*[@name='account_edit_name']"
			)->single()
		);

		$this->assertValue(
			ItMockStores::SESSION_ACCOUNT_WEBSITE,
			$actual->html->path()->collections(
				"//*[@name='account_edit_website']"
			)->single()
		);

		$this->assertValue(
			ItMockStores::SESSION_ACCOUNT_DESCRIPTION,
			$actual->html->path()->collections(
				"//*[@name='account_edit_description']"
			)->single()
		);
	}

	public function test_user_edit_post_empty_name()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
			body: ItBody::form([
				'account_edit_name' => '',
			])
		);
		$actual = $this->call(HttpMethod::Post, '/account/user/edit', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
		});

		$this->assertStatusOk($actual);

		$this->assertVisibleCommonError([], $actual);

		$this->assertValue(
			$options->body->content['account_edit_name'],
			$actual->html->path()->collections(
				"//*[@name='account_edit_name']"
			)->single()
		);
		$this->assertVisibleTargetError(
			['未入力です'],
			"account_edit_name",
			$actual
		);
	}

	public static function provider_user_edit_post_range_name()
	{
		return [
			[Text::repeat('a', AccountValidator::USER_NAME_RANGE_MIN - 1)],
			[Text::repeat('z', AccountValidator::USER_NAME_RANGE_MAX + 1)],
			[Text::repeat('🫠', AccountValidator::USER_NAME_RANGE_MIN - 1)],
			[Text::repeat('🫠', AccountValidator::USER_NAME_RANGE_MAX + 1)],
		];
	}

	#[DataProvider('provider_user_edit_post_range_name')]
	public function test_user_edit_post_range_name(string $name)
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
			body: ItBody::form([
				'account_edit_name' => $name,
			])
		);
		$actual = $this->call(HttpMethod::Post, '/account/user/edit', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
		});

		$this->assertStatusOk($actual);

		$this->assertVisibleCommonError([], $actual);

		$this->assertValue(
			$options->body->content['account_edit_name'],
			$actual->html->path()->collections(
				"//*[@name='account_edit_name']"
			)->single()
		);
		$this->assertVisibleTargetError(
			[Text::format('%d から %d 文字以内で入力してください', AccountValidator::USER_NAME_RANGE_MIN, AccountValidator::USER_NAME_RANGE_MAX)],
			"account_edit_name",
			$actual
		);
	}


	public function test_user_edit_post_invalid_url()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
			body: ItBody::form([
				'account_edit_name' => ItMockStores::SESSION_ACCOUNT_NAME,
				'account_edit_website' => '123',
			])
		);
		$actual = $this->call(HttpMethod::Post, '/account/user/edit', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
		});

		$this->assertStatusOk($actual);

		$this->assertVisibleCommonError([], $actual);

		$this->assertValue(
			$options->body->content['account_edit_name'],
			$actual->html->path()->collections(
				"//*[@name='account_edit_name']"
			)->single()
		);

		$this->assertValue(
			$options->body->content['account_edit_website'],
			$actual->html->path()->collections(
				"//*[@name='account_edit_website']"
			)->single()
		);
		$this->assertVisibleTargetError(
			['URLが正しくありません'],
			"account_edit_website",
			$actual
		);
	}

	public function test_user_edit_post_empty_name_invalid_url()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
			body: ItBody::form([
				'account_edit_name' => '',
				'account_edit_website' => '123',
			])
		);
		$actual = $this->call(HttpMethod::Post, '/account/user/edit', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
		});

		$this->assertStatusOk($actual);

		$this->assertVisibleCommonError([], $actual);

		$this->assertValue(
			$options->body->content['account_edit_name'],
			$actual->html->path()->collections(
				"//*[@name='account_edit_name']"
			)->single()
		);
		$this->assertVisibleTargetError(
			['未入力です'],
			"account_edit_name",
			$actual
		);

		$this->assertValue(
			$options->body->content['account_edit_website'],
			$actual->html->path()->collections(
				"//*[@name='account_edit_website']"
			)->single()
		);
		$this->assertVisibleTargetError(
			['URLが正しくありません'],
			"account_edit_website",
			$actual
		);
	}

	public static function provider_user_edit_post_submit()
	{
		return [
			[
				Text::repeat('🫠', AccountValidator::USER_NAME_RANGE_MIN),
				'',
				'',
			],
			[
				Text::repeat('🫠', AccountValidator::USER_NAME_RANGE_MAX),
				'',
				'',
			],
			[
				Text::repeat('🫠', AccountValidator::USER_NAME_RANGE_MIN),
				'https://example.com',
				'',
			],
			[
				Text::repeat('🫠', AccountValidator::USER_NAME_RANGE_MIN),
				'https://example.com',
				Text::repeat('🫠', AccountValidator::USER_DESCRIPTION_LENGTH),
			],
		];
	}

	#[DataProvider('provider_user_edit_post_submit')]
	public function test_user_edit_post_submit(string $name, string $url, string $description)
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
			body: ItBody::form([
				'account_edit_name' => $name,
				'account_edit_website' => $url,
				'account_edit_description' => $description,
			])
		);
		$actual = $this->call(HttpMethod::Post, '/account/user/edit', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
		});

		$this->assertRedirectPath(HttpStatus::Found, 'account/user', null, $actual);

		$context = $actual->openDB();
		$auditResult = $this->getMaybeLatestAuditLog($context);
		$this->assertSame(ItMockStores::SESSION_ACCOUNT_USER_ID, $auditResult->fields['user_id']);
		$this->assertSame(AuditLog::USER_EDIT, $auditResult->fields['event']);

		$usersEntityDao = new UsersEntityDao($context);
		$userEditData = $usersEntityDao->selectUserEditData(ItMockStores::SESSION_ACCOUNT_USER_ID);
		$this->assertSame($name, $userEditData->fields['name']);
		$this->assertSame($url, $userEditData->fields['website']);
		$this->assertSame($description, $userEditData->fields['description']);
	}

	public function test_user_api_get_empty()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Get, '/account/user/api', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
		});

		$this->assertStatusOk($actual);

		$this->assertTextNode(
			'APIキーを用いてAPIを実行することができます。',
			$actual->html->path()->collections(
				"//*[contains(@class, 'page-account-api')]//dt[contains(text(), '説明')]/following-sibling::dd[1]"
			)->single()
		);

		$this->assertTextNode(
			'APIキーは登録されていません。',
			$actual->html->path()->collections(
				"//*[contains(@class, 'page-account-api')]//dt[contains(text(), 'APIキー')]/following-sibling::dd[1]"
			)->single()
		);

		$this->assertTextNode(
			'登録',
			$actual->html->path()->collections(
				"//*[contains(@class, 'page-account-api')]//dd[contains(@class, 'action')]/button"
			)->single()
		);
	}

	public function test_user_api_get_exists()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Get, '/account/user/api', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);
			$apiKeysEntityDao = new ApiKeysEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
			$apiKeysEntityDao->insertApiKey(ItMockStores::SESSION_ACCOUNT_USER_ID, 'KEY', 'SECRET');
		});

		$this->assertStatusOk($actual);

		$this->assertTextNode(
			'APIキーを用いてAPIを実行することができます。',
			$actual->html->path()->collections(
				"//*[contains(@class, 'page-account-api')]//dt[contains(text(), '説明')]/following-sibling::dd[1]"
			)->single()
		);

		$this->assertTextNode(
			'KEY',
			$actual->html->path()->collections(
				"//*[contains(@class, 'page-account-api')]//dt[contains(text(), 'APIキー')]/following-sibling::dd[1]/table//tr[1]/td[1]"
			)->single()
		);

		$this->assertCount(
			0,
			$actual->html->path()->collections(
				"//*[contains(@class, 'page-account-api')]//dt[contains(text(), 'APIキー')]/following-sibling::dd[1]/table//tr[3]/td"
			)
		);

		$this->assertTextNode(
			'削除',
			$actual->html->path()->collections(
				"//*[contains(@class, 'page-account-api')]//dd[contains(@class, 'action')]/button"
			)->single()
		);
	}

	public function test_user_api_register()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Post, '/account/user/api', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
		});

		$this->assertRedirectPath(HttpStatus::Found, 'account/user/api', null, $actual);

		$context = $actual->openDB();
		$auditResult = $this->getMaybeLatestAuditLog($context);
		$this->assertSame(ItMockStores::SESSION_ACCOUNT_USER_ID, $auditResult->fields['user_id']);
		$this->assertSame(AuditLog::USER_API_KEY_REGISTER, $auditResult->fields['event']);

		$apiKeysEntityDao = new ApiKeysEntityDao($context);
		$apiKeyResult = $apiKeysEntityDao->selectApiKeyByUserId(ItMockStores::SESSION_ACCOUNT_USER_ID);
		$apiKeys = [
			'api_key' => $apiKeyResult->fields['api_key'],
			'secret_key' => $apiKeyResult->fields['secret_key'],
		];

		// リダイレクト

		$options2 = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual2 = $this->call(HttpMethod::Get, '/account/user/api', $options2, null, $actual);

		$this->assertStatusOk($actual2);

		//$actual2->html->raw->saveHTMLFile("X:\\a.html");

		$this->assertTextNode(
			$apiKeys['api_key'],
			$actual2->html->path()->collections(
				"//*[contains(@class, 'page-account-api')]//dt[contains(text(), 'APIキー')]/following-sibling::dd[1]/table//tr[1]/td[1]"
			)->single()
		);

		$this->assertTextNode(
			$apiKeys['secret_key'],
			$actual2->html->path()->collections(
				"//*[contains(@class, 'page-account-api')]//dt[contains(text(), 'APIキー')]/following-sibling::dd[1]/table//tr[3]/td"
			)->single()
		);
	}

	public function test_user_api_unregister()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Post, '/account/user/api', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);
			$apiKeysEntityDao = new ApiKeysEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
			$apiKeysEntityDao->insertApiKey(ItMockStores::SESSION_ACCOUNT_USER_ID, 'KEY', 'SECRET');
		});

		$this->assertRedirectPath(HttpStatus::Found, 'account/user/api', null, $actual);

		$context = $actual->openDB();
		$auditResult = $this->getMaybeLatestAuditLog($context);
		$this->assertSame(ItMockStores::SESSION_ACCOUNT_USER_ID, $auditResult->fields['user_id']);
		$this->assertSame(AuditLog::USER_API_KEY_UNREGISTER, $auditResult->fields['event']);

		$apiKeysEntityDao = new ApiKeysEntityDao($context);
		$this->assertFalse($apiKeysEntityDao->selectExistsApiKeyByUserId(ItMockStores::SESSION_ACCOUNT_USER_ID));
	}

	public function test_user_password_get()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Get, '/account/user/password', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
			$userAuthenticationsEntityDao->insertUserAuthentication(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_PASSWORD);
		});

		$this->assertStatusOk($actual);

		$this->assertTitle('パスワード変更', $actual);

		$this->assertTextNode(
			'',
			$actual->html->path()->collections(
				"//*[@id='account_password_current']"
			)->single()
		);

		$this->assertTextNode(
			'',
			$actual->html->path()->collections(
				"//*[@id='account_password_new']"
			)->single()
		);

		$this->assertTextNode(
			'',
			$actual->html->path()->collections(
				"//*[@id='account_password_confirm']"
			)->single()
		);
	}
}
