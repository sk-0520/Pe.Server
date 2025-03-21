<?php

declare(strict_types=1);

namespace PeServerUT\App\Models\Dao\Entities;

use PeServer\App\Models\Dao\Entities\AccessLogsEntityDao;
use PeServer\App\Models\Dao\Entities\ApiKeysEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Data\Dto\AccessLogDto;
use PeServer\Core\Throws\DatabaseException;
use PeServer\Core\Throws\DatabaseInvalidQueryException;
use PeServerTest\TestClass;
use PeServerTest\UtAppDatabase;

class ApiKeysEntityDaoTest extends TestClass
{
	#region function

	public function test_selectExistsApiKeyByUserId()
	{
		$db = new UtAppDatabase();

		$context = $db->default->open();

		$usersEntityDao = new UsersEntityDao($context);
		$usersEntityDao->insertUser("userId", "loginId", "level", "state", "userName", "email", 0, "website", "description", "note");

		$apiKeysEntityDao = new ApiKeysEntityDao($context);
		$apiKeysEntityDao->insertApiKey("userId", "key", "secret");

		$actual = $apiKeysEntityDao->selectExistsApiKeyByUserId('userId');
		$this->assertTrue($actual);
	}

	public function test_selectExistsApiKeyByUserId_empty()
	{
		$db = new UtAppDatabase();

		$context = $db->default->open();

		$apiKeysEntityDao = new ApiKeysEntityDao($context);

		$actual = $apiKeysEntityDao->selectExistsApiKeyByUserId('empty');
		$this->assertFalse($actual);
	}

	public function test_selectExistsApiKeyByApiKey()
	{
		$db = new UtAppDatabase();

		$context = $db->default->open();

		$usersEntityDao = new UsersEntityDao($context);
		$usersEntityDao->insertUser("userId", "loginId", "level", "state", "userName", "email", 0, "website", "description", "note");

		$apiKeysEntityDao = new ApiKeysEntityDao($context);
		$apiKeysEntityDao->insertApiKey("userId", "key", "secret");

		$actual = $apiKeysEntityDao->selectExistsApiKeyByApiKey('key');
		$this->assertTrue($actual);
	}

	public function test_selectExistsApiKeyByApiKey_empty()
	{
		$db = new UtAppDatabase();

		$context = $db->default->open();

		$apiKeysEntityDao = new ApiKeysEntityDao($context);
		$actual = $apiKeysEntityDao->selectExistsApiKeyByApiKey('empty');

		$this->assertFalse($actual);
	}

	public function test_selectApiKeyByUserId()
	{
		$db = new UtAppDatabase();

		$context = $db->default->open();

		$usersEntityDao = new UsersEntityDao($context);
		$usersEntityDao->insertUser("userId", "loginId", "level", "state", "userName", "email", 0, "website", "description", "note");

		$apiKeysEntityDao = new ApiKeysEntityDao($context);
		$apiKeysEntityDao->insertApiKey("userId", "key", "secret");

		$actual = $apiKeysEntityDao->selectApiKeyByUserId('userId');
		$this->assertSame("userId", $actual->fields['user_id']);
		$this->assertSame("key", $actual->fields['api_key']);
		$this->assertSame("secret", $actual->fields['secret_key']);
	}

	public function test_selectApiKeyByUserId_throw()
	{
		$db = new UtAppDatabase();

		$context = $db->default->open();

		$apiKeysEntityDao = new ApiKeysEntityDao($context);

		$this->expectException(DatabaseInvalidQueryException::class);

		$apiKeysEntityDao->selectApiKeyByUserId('userId');
		$this->fail();
	}

	public function test_insertApiKey_throw()
	{
		$db = new UtAppDatabase();

		$context = $db->default->open();

		$usersEntityDao = new UsersEntityDao($context);
		$usersEntityDao->insertUser("userId", "loginId", "level", "state", "userName", "email", 0, "website", "description", "note");

		$apiKeysEntityDao = new ApiKeysEntityDao($context);
		$apiKeysEntityDao->insertApiKey("userId", "key", "secret");

		$this->expectException(DatabaseException::class);

		$apiKeysEntityDao->insertApiKey("userId", "key", "secret");
		$this->fail();
	}

	public function test_deleteApiKeyByUserId()
	{
		$db = new UtAppDatabase();

		$context = $db->default->open();

		$usersEntityDao = new UsersEntityDao($context);
		$usersEntityDao->insertUser("userId", "loginId", "level", "state", "userName", "email", 0, "website", "description", "note");

		$apiKeysEntityDao = new ApiKeysEntityDao($context);
		$apiKeysEntityDao->insertApiKey("userId", "key", "secret");

		$actual = $apiKeysEntityDao->deleteApiKeyByUserId("userId");
		$this->assertSame(1, $actual);
	}

	public function test_deleteApiKeyByUserId_empty()
	{
		$db = new UtAppDatabase();

		$context = $db->default->open();

		$apiKeysEntityDao = new ApiKeysEntityDao($context);

		$actual = $apiKeysEntityDao->deleteApiKeyByUserId("userId");
		$this->assertSame(0, $actual);
	}

	#endregion
}
