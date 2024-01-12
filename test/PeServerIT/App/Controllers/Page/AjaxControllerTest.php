<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\Core\Http\HttpMethod;
use PeServerTest\ItControllerClass;
use PeServerTest\ItMockStores;
use PeServerTest\ItOptions;
use PeServerTest\ItSetup;
use PeServerTest\ItBody;

class AjaxControllerTest extends ItControllerClass
{
	public function test_markdown()
	{
		$options = new ItOptions(
			stores: ItMockStores::account(UserLevel::USER),
			body: ItBody::json([
				'source' => '# H1'
			])
		);
		$actual = $this->call(HttpMethod::Post, '/ajax/markdown', $options, function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);
		});

		$this->assertStatusOk($actual);
		$this->assertTrue($actual->isJson());
		$this->assertIsArray($actual->json);
		$this->assertStringContainsString('<h1>H1</h1>', $actual->json['data']['markdown']);
	}
}
