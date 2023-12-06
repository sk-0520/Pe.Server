<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\App\Controllers\Page\HomeController;
use PeServer\App\Models\Dao\Domain\UserDomainDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServer\Core\Throws\HttpStatusException;
use PeServerTest\ItLoginTrait;
use PeServerTest\ItUseDatabaseTrait;
use PeServerTest\ItMockStores;
use PeServerTest\ItOptions;
use PeServerTest\TestControllerClass;
use PeServerUT\Core\DI\C;

class AccountControllerTest extends TestControllerClass
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

	/** @dataProvider provider_it_notLogin */
	public function test_it_notLogin(string $path)
	{
		$this->_test_notLogin($path);
	}

	public function test_user()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
		);
		$actual = $this->call(HttpMethod::Get, '/account', $options, function (IDiContainer $container, IDatabaseContext $databaseContext) {
			$usersEntityDao = new UsersEntityDao($databaseContext);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, 'email', 0, 'w', 'd', 'n');
			$userAuthenticationsEntityDao->insertUserAuthentication(ItMockStores::SESSION_ACCOUNT_USER_ID, 'p');
		});

		$this->assertStatusOk($actual);
		$this->assertTitle('ユーザー情報', $actual);

		$this->assertTextNode(
			ItMockStores::SESSION_ACCOUNT_USER_ID,
			$actual->html->path()->collection(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), 'ユーザーID')]/following-sibling::dd[1]//*[@data-role='value']"
			)->first()
		);

		$this->assertTextNode(
			ItMockStores::SESSION_ACCOUNT_LOGIN_ID,
			$actual->html->path()->collection(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), 'ログインID')]/following-sibling::dd[1]//*[@data-role='value']"
			)->first()
		);

		$this->assertTextNode(
			UserLevel::toString(UserLevel::USER),
			$actual->html->path()->collection(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), '権限')]/following-sibling::dd[1][@data-role='value']"
			)->first()
		);

		$this->assertTextNode(
			ItMockStores::SESSION_ACCOUNT_NAME,
			$actual->html->path()->collection(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), '名前')]/following-sibling::dd[1][@data-role='value']"
			)->first()
		);

		$this->assertTextNode(
			'w',
			$actual->html->path()->collection(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), 'Webサイト')]/following-sibling::dd[1][@data-role='value']"
			)->first()
		);

		$this->assertTextNode(
			'未登録',
			$actual->html->path()->collection(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), 'プラグイン')]/following-sibling::dd[1][@data-role='value']"
			)->first()
		);
	}

	public function test_login_get_notLogin()
	{
		$actual = $this->call(HttpMethod::Get, '/account/login');

		$this->assertStatusOK($actual);
		$this->assertTitle('ログイン', $actual);

		$this->assertTextNode(
			'',
			$actual->html->path()->collection(
				"//*[@id='content']/form[1][@action='/account/login']//*[contains(@class,'input')]//dt[text()='ログインID']/following-sibling::dd[1]"
			)->first()
		);

		$this->assertTextNode(
			'',
			$actual->html->path()->collection(
				"//*[@id='content']/form[1][@action='/account/login']//*[contains(@class,'input')]//dt[text()='パスワード']/following-sibling::dd[1]"
			)->first()
		);
	}


	public function test_login_post_empty_input()
	{
		$options = new ItOptions(
			stores: ItMockStores::anonymous(login: true),
		);
		$actual = $this->call(HttpMethod::Post, '/account/login', $options);

		$this->assertStatusOK($actual);
		$this->assertTitle('ログイン', $actual);

		$this->assertVisibleCommonError($actual, ['ログインID/パスワードが正しくありません']);

		$this->assertTextNode(
			'',
			$actual->html->path()->collection(
				"//*[@id='content']/form[1][@action='/account/login']//*[contains(@class,'input')]//dt[text()='ログインID']/following-sibling::dd[1]"
			)->first()
		);

		$this->assertTextNode(
			'',
			$actual->html->path()->collection(
				"//*[@id='content']/form[1][@action='/account/login']//*[contains(@class,'input')]//dt[text()='パスワード']/following-sibling::dd[1]"
			)->first()
		);
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

	/** @dataProvider provider_login_post_notLogin_throw */
	public function test_login_post_notLogin_throw(ItMockStores $input)
	{
		$options = new ItOptions(
			stores: $input,
		);
		$this->expectException(HttpStatusException::class);
		$this->call(HttpMethod::Post, '/account/login', $options);
	}
}
