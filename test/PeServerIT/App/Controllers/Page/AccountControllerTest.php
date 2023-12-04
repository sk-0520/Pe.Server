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
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServerTest\ItLoginTrait;
use PeServerTest\ItSetupDatabaseTrait;
use PeServerTest\ItUseDatabase;
use PeServerTest\MockStores;
use PeServerTest\TestControllerClass;

class AccountControllerTest extends TestControllerClass
{
	use ItUseDatabase;
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

	public function test_index()
	{
		$actual = $this->call(HttpMethod::Get, '/account', MockStores::account(UserLevel::USER), function (IDiContainer $container, $databaseContext) {
			$usersEntityDao = new UsersEntityDao($databaseContext);
			$userAuthenticationsEntityDao = new UserAuthenticationsEntityDao($databaseContext);

			$usersEntityDao->insertUser(MockStores::SESSION_ACCOUNT_USER_ID, MockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, MockStores::SESSION_ACCOUNT_NAME, 'email', 0, 'w', 'd', 'n');
			$userAuthenticationsEntityDao->insertUserAuthentication(MockStores::SESSION_ACCOUNT_USER_ID, 'p');
		});

		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTitle('ユーザー情報', $actual);

		$this->assertTextElement(
			MockStores::SESSION_ACCOUNT_USER_ID,
			$actual->html->path()->collection(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), 'ユーザーID')]/following-sibling::dd//*[@data-role='value']"
			)->first()
		);

		$this->assertTextElement(
			MockStores::SESSION_ACCOUNT_LOGIN_ID,
			$actual->html->path()->collection(
				"//dl[contains(@class, 'page-account-user')]/dt[contains(text(), 'ログインID')]/following-sibling::dd//*[@data-role='value']"
			)->first()
		);
	}
}
