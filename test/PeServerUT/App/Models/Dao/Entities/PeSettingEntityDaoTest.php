<?php

declare(strict_types=1);

namespace PeServerUT\App\Models\Dao\Entities;

use PeServer\App\Models\Dao\Entities\AccessLogsEntityDao;
use PeServer\App\Models\Dao\Entities\ApiKeysEntityDao;
use PeServer\App\Models\Dao\Entities\PeSettingEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Data\Dto\AccessLogDto;
use PeServer\Core\Throws\DatabaseException;
use PeServer\Core\Throws\DatabaseInvalidQueryException;
use PeServerTest\TestClass;
use PeServerTest\UtAppDatabase;

class PeSettingEntityDaoTest extends TestClass
{
	#region function

	public function test_selectPeSettingVersion()
	{
		$db = new UtAppDatabase();

		$context = $db->default->open();

		$peSettingEntityDao = new PeSettingEntityDao($context);

		$actual = $peSettingEntityDao->selectPeSettingVersion();
		$this->assertSame("0.00.000", $actual); // 初期化時点で格納済みなので取得可能
	}

	public function test_updatePeSettingVersion()
	{
		$db = new UtAppDatabase();

		$context = $db->default->open();

		$peSettingEntityDao = new PeSettingEntityDao($context);

		$peSettingEntityDao->updatePeSettingVersion("1.2.3"); // 初期化時点で格納済みのため更新可能
		$actual = $peSettingEntityDao->selectPeSettingVersion();
		$this->assertSame("1.2.3", $actual);
	}

	#endregion
}
