<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\App\Controllers\Page\HomeController;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Domain\UserDomainDao;
use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuditLogsEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Domain\PluginState;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\Core\Cryptography;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Web\UrlPath;
use PeServerTest\ItBody;
use PeServerTest\ItLoginTrait;
use PeServerTest\ItUseDatabaseTrait;
use PeServerTest\ItMockStores;
use PeServerTest\ItOptions;
use PeServerTest\ItControllerClass;
use PeServerUT\Core\DI\C;
use PHPUnit\Framework\Attributes\DataProvider;
use PeServer\Core\Html\HtmlTagElement;
use PeServer\Core\Text;
use PeServerTest\ItSetup;
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

		$this->assertVisibleCommonError($actual, ['ログインID/パスワードが正しくありません']);

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

	public function test_login_post_failure()
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

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, 'email', 0, 'w', 'd', 'n');
			$userAuthenticationsEntityDao->insertUserAuthentication(ItMockStores::SESSION_ACCOUNT_USER_ID, Cryptography::hashPassword('@'));
		});

		$this->assertStatusOK($actual);
		$this->assertTitle('ログイン', $actual);

		$this->assertVisibleCommonError($actual, ['ログインID/パスワードが正しくありません']);

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

		$context = $actual->openDB();
		$userAuditLogsEntityDao = new UserAuditLogsEntityDao($context);
		$logId = $userAuditLogsEntityDao->selectLastLogId();
		$auditResult = $context->querySingle('select * from user_audit_logs where sequence = :sequence', ['sequence' => $logId]);
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

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, 'email', 0, 'w', 'd', 'n');
			$userAuthenticationsEntityDao->insertUserAuthentication(ItMockStores::SESSION_ACCOUNT_USER_ID, Cryptography::hashPassword('@'));
		});

		$this->assertStatusOK($actual);
		$this->assertTitle('ログイン', $actual);

		$this->assertVisibleCommonError($actual, ['ログインID/パスワードが正しくありません']);

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

	public function test_login_post_success()
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

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, 'email', 0, 'w', 'd', 'n');
			$userAuthenticationsEntityDao->insertUserAuthentication(ItMockStores::SESSION_ACCOUNT_USER_ID, Cryptography::hashPassword('password'));
		});

		$this->assertRedirectPath(HttpStatus::Found, '/account', null, $actual);

		// $context = $actual->openDB();
		// $userAuditLogsEntityDao = new UserAuditLogsEntityDao($context);
		// $logId = $userAuditLogsEntityDao->selectLastLogId();
		// $auditResult = $context->querySingle('select * from user_audit_logs where sequence = :sequence', ['sequence' => $logId]);
		// $this->assertSame(ItMockStores::SESSION_ACCOUNT_USER_ID, $auditResult->fields['user_id']);
		// $this->assertSame(AuditLog::LOGIN_SUCCESS, $auditResult->fields['event']);
	}

	public function test_login_post_login()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Post, '/account/login', $options);
		$this->assertRedirectPath(HttpStatus::Found, '/account/user', null, $actual);
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

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, 'email', 0, 'w', 'd', 'n');
			$userAuthenticationsEntityDao->insertUserAuthentication(ItMockStores::SESSION_ACCOUNT_USER_ID, Cryptography::hashPassword('@'));
		});
		$this->assertRedirectPath(HttpStatus::Found, '', null, $actual);

		// $context = $actual->openDB();
		// $userAuditLogsEntityDao = new UserAuditLogsEntityDao($context);
		// $logId = $userAuditLogsEntityDao->selectLastLogId();
		// $auditResult = $context->querySingle('select * from user_audit_logs where sequence = :sequence', ['sequence' => $logId]);
		// $this->assertSame(ItMockStores::SESSION_ACCOUNT_USER_ID, $auditResult->fields['user_id']);
		// $this->assertSame(AuditLog::LOGOUT, $auditResult->fields['event']);
	}

	public function test_user()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Get, '/account', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, 'email', 0, 'w', 'd', 'n');
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
			'w',
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

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID . '-OTHER', ItMockStores::SESSION_ACCOUNT_LOGIN_ID . '-OTHER', UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, 'email', 0, 'w', 'd', 'n');
			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, 'email', 0, 'w', 'd', 'n');

			$pluginsEntityDao->insertPlugin('A0', ItMockStores::SESSION_ACCOUNT_USER_ID . '-OTHER', 'PLUGIN-A0', 'plugin-a0', PluginState::ENABLED, 'P-A0-D', '');
			$pluginsEntityDao->insertPlugin('A2', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-A2', 'plugin-a2', PluginState::ENABLED, 'P-A-D2', '');
			$pluginsEntityDao->insertPlugin('A1', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-A1', 'plugin-a1', PluginState::ENABLED, 'P-A-D1', '');
			$pluginsEntityDao->insertPlugin('B0', ItMockStores::SESSION_ACCOUNT_USER_ID . '-OTHER', 'PLUGIN-B0', 'plugin-b0', PluginState::CHECK_FAILED, 'P-B0-D', '');
			$pluginsEntityDao->insertPlugin('B2', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-B2', 'plugin-b2', PluginState::CHECK_FAILED, 'P-B-D2', '');
			$pluginsEntityDao->insertPlugin('B1', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-B1', 'plugin-b1', PluginState::CHECK_FAILED, 'P-B-D1', '');
			$pluginsEntityDao->insertPlugin('C0', ItMockStores::SESSION_ACCOUNT_USER_ID . '-OTHER', 'PLUGIN-C0', 'plugin-c0', PluginState::DISABLED, 'P-C0-D', '');
			$pluginsEntityDao->insertPlugin('C2', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-C2', 'plugin-c2', PluginState::DISABLED, 'P-C-D2', '');
			$pluginsEntityDao->insertPlugin('C1', ItMockStores::SESSION_ACCOUNT_USER_ID, 'PLUGIN-C1', 'plugin-c1', PluginState::DISABLED, 'P-C-D1', '');
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
				'id' => 'C1',
				'name' => 'PLUGIN-C1',
			],
			[
				'id' => 'C2',
				'name' => 'PLUGIN-C2',
			],
		];

		$actualItems = $actual->html->path()->collections(
			"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), 'プラグイン')]/following-sibling::dd[1][@data-role='value']//li//a"
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

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, 'email', 0, 'w', 'd', 'n');
		});

		$this->assertStatusOk($actual);

		$this->assertValue(
			ItMockStores::SESSION_ACCOUNT_NAME,
			$actual->html->path()->collections(
				"//*[@name='account_edit_name']"
			)->single()
		);

		$this->assertValue(
			'w',
			$actual->html->path()->collections(
				"//*[@name='account_edit_website']"
			)->single()
		);

		$this->assertValue(
			'd',
			$actual->html->path()->collections(
				"//*[@name='account_edit_description']"
			)->single()
		);
	}
}
