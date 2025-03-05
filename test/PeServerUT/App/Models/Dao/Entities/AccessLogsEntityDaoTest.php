<?php

declare(strict_types=1);

namespace PeServerUT\App\Models\Dao\Entities;

use PeServer\App\Models\Dao\Entities\AccessLogsEntityDao;
use PeServer\App\Models\Data\Dto\AccessLogDto;
use PeServerTest\TestClass;
use PeServerTest\UtAppDatabase;

class AccessLogsEntityDaoTest extends TestClass
{
	#region function

	public function test_insertAccessLog()
	{
		$db = new UtAppDatabase();

		$actual1 = $db->default->open()->selectSingleCount("select count(*) from access_logs");
		$this->assertSame(0, $actual1);

		$dao = new AccessLogsEntityDao($db->default->open());

		$dto = new AccessLogDto();
		$dao->insertAccessLog($dto);
		$actual2 = $db->default->open()->selectSingleCount("select count(*) from access_logs");

		$this->assertSame(1, $actual2);
	}

	#endregion
}
