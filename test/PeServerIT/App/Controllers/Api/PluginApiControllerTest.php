<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Api;

use PeServer\App\Models\Dao\Entities\PluginsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Domain\PluginState;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Serialization\JsonSerializer;
use PeServerTest\ItBody;
use PeServerTest\ItControllerClass;
use PeServerTest\ItMockStores;
use PeServerTest\ItOptions;
use PeServerTest\ItSetup;
use PeServerTest\ItUseDatabaseCacheTrait;
use PeServerTest\ItUseDatabaseTrait;
use PHPUnit\Framework\Attributes\DataProvider;

class PluginApiControllerTest extends ItControllerClass
{
	use ItUseDatabaseTrait;
	use ItUseDatabaseCacheTrait;

	public static function provider_exists()
	{
		return [
			[
				[
					'plugin_id' => true,
					'plugin_name' => false,
				],
				ItBody::json([
					'plugin_id' => 'plugin-id:1',
				])
			],
			[
				[
					'plugin_id' => false,
					'plugin_name' => true,
				],
				ItBody::json([
					'plugin_name' => 'plugin-name:2',
				])
			],
			[
				[
					'plugin_id' => true,
					'plugin_name' => true,
				],
				ItBody::json([
					'plugin_id' => 'plugin-id:1',
					'plugin_name' => 'plugin-name:2',
				])
			],
			[
				[
					'plugin_id' => false,
					'plugin_name' => false,
				],
				ItBody::json([
					'plugin_id' => 'plugin-id:3',
					'plugin_name' => 'plugin-name:3',
				])
			],
		];
	}

	#[DataProvider('provider_exists')]
	public function test_exists(array $expected, ItBody $input)
	{
		$options = new ItOptions(
			body: $input
		);
		$actual = $this->call(HttpMethod::Post, 'api/plugin/exists', $options, setup: function (ItSetup $setup) {
			$usersEntityDao = new UsersEntityDao($setup->databaseContext);
			$pluginsEntityDao = new PluginsEntityDao($setup->databaseContext);

			$usersEntityDao->insertUser(ItMockStores::SESSION_ACCOUNT_USER_ID, ItMockStores::SESSION_ACCOUNT_LOGIN_ID, UserLevel::USER, UserState::ENABLED, ItMockStores::SESSION_ACCOUNT_NAME, ItMockStores::SESSION_ACCOUNT_EMAIL, ItMockStores::SESSION_ACCOUNT_MARKER, ItMockStores::SESSION_ACCOUNT_WEBSITE, ItMockStores::SESSION_ACCOUNT_DESCRIPTION, ItMockStores::SESSION_ACCOUNT_NOTE);

			$pluginsEntityDao->insertPlugin(
				'plugin-id:1',
				ItMockStores::SESSION_ACCOUNT_USER_ID,
				'plugin-name:1',
				'PLUGIN NAME 1',
				PluginState::ENABLED,
				'description:1',
				'note:1'
			);

			$pluginsEntityDao->insertPlugin(
				'plugin-id:2',
				ItMockStores::SESSION_ACCOUNT_USER_ID,
				'plugin-name:2',
				'PLUGIN NAME 2',
				PluginState::ENABLED,
				'description:2',
				'note:2'
			);
		});

		$this->assertStatusOk($actual);
		$this->assertTrue($actual->isJson());
		$this->assertSame($expected, $actual->json['data']);
	}
}
