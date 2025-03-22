<?php

declare(strict_types=1);

namespace PeServerUT\App\Models\Dao\Entities;

use DateTimeImmutable;
use DateTimeInterface;
use PeServer\App\Models\Dao\Entities\SessionsEntityDao;
use PeServer\Core\Utc;
use PeServerTest\TestClass;
use PeServerTest\UtAppDatabase;

class SessionsEntityDaoTest extends TestClass
{
	#region function

	public function test_selectExistsBySessionId()
	{
		$db = new UtAppDatabase();

		$context = $db->session->open();

		$sessionsEntityDao = new SessionsEntityDao($context);

		$sessionsEntityDao->upsertSessionDataBySessionId("id", "data", Utc::create());
		$actual = $sessionsEntityDao->selectExistsBySessionId("id");
		$this->assertTrue($actual);
	}

	public function test_selectExistsBySessionId_empty()
	{
		$db = new UtAppDatabase();

		$context = $db->session->open();

		$sessionsEntityDao = new SessionsEntityDao($context);

		$actual = $sessionsEntityDao->selectExistsBySessionId("id");
		$this->assertFalse($actual);
	}


	public function test_selectSessionDataBySessionId()
	{
		$db = new UtAppDatabase();

		$context = $db->session->open();

		$sessionsEntityDao = new SessionsEntityDao($context);

		$sessionsEntityDao->upsertSessionDataBySessionId("id", "data", Utc::create());
		$actual = $sessionsEntityDao->selectSessionDataBySessionId("id");
		$this->assertSame("data", $actual);
	}

	public function test_selectSessionDataBySessionId_null()
	{
		$db = new UtAppDatabase();

		$context = $db->session->open();

		$sessionsEntityDao = new SessionsEntityDao($context);

		$actual = $sessionsEntityDao->selectSessionDataBySessionId("id");
		$this->assertNull($actual);
	}

	public function test_upsertSessionDataBySessionId()
	{
		$db = new UtAppDatabase();

		$context = $db->session->open();

		$sessionsEntityDao = new SessionsEntityDao($context);

		$sessionsEntityDao->upsertSessionDataBySessionId("id", "data", Utc::create());

		$actualInsert = $sessionsEntityDao->selectSessionDataBySessionId("id");
		$this->assertSame("data", $actualInsert);

		$sessionsEntityDao->upsertSessionDataBySessionId("id", "data2", Utc::create());
		$actualUpdate = $sessionsEntityDao->selectSessionDataBySessionId("id");
		$this->assertSame("data2", $actualUpdate);
	}

	public function test_updateSessionBySessionId()
	{
		$db = new UtAppDatabase();

		$context = $db->session->open();

		$sessionsEntityDao = new SessionsEntityDao($context);

		$sessionsEntityDao->upsertSessionDataBySessionId("id", "data", Utc::create());

		$actualInsert = $sessionsEntityDao->selectSessionDataBySessionId("id");
		$this->assertSame("data", $actualInsert);

		$actual = $sessionsEntityDao->updateSessionBySessionId("id", "data2", Utc::create());
		$this->assertTrue($actual);
		$actualUpdate = $sessionsEntityDao->selectSessionDataBySessionId("id");
		$this->assertSame("data2", $actualUpdate);
	}

	public function test_updateSessionBySessionId_empty()
	{
		$db = new UtAppDatabase();

		$context = $db->session->open();

		$sessionsEntityDao = new SessionsEntityDao($context);

		$actual = $sessionsEntityDao->updateSessionBySessionId("id", "data", Utc::create());
		$this->assertFalse($actual);
	}

	public function test_deleteSessionBySessionId()
	{
		$db = new UtAppDatabase();

		$context = $db->session->open();

		$sessionsEntityDao = new SessionsEntityDao($context);

		$sessionsEntityDao->upsertSessionDataBySessionId("id", "data", Utc::create());

		$actual = $sessionsEntityDao->deleteSessionBySessionId("id", "data2", Utc::create());
		$this->assertTrue($actual);
		$actualExists = $sessionsEntityDao->selectExistsBySessionId("id");
		$this->assertFalse($actualExists);
	}

	public function test_deleteSessionBySessionId_empty()
	{
		$db = new UtAppDatabase();

		$context = $db->session->open();

		$sessionsEntityDao = new SessionsEntityDao($context);

		$actual = $sessionsEntityDao->deleteSessionBySessionId("id", "data2", Utc::create());
		$this->assertFalse($actual);
	}

	public function test_deleteOldSessions()
	{
		$db = new UtAppDatabase();

		$context = $db->session->open();

		$sessionsEntityDao = new SessionsEntityDao($context);

		$sessionsEntityDao->upsertSessionDataBySessionId("id-0", "data", DateTimeImmutable::createFromFormat(DateTimeInterface::ISO8601_EXPANDED, '2025-03-22T20:00:00+00:00'));
		$sessionsEntityDao->upsertSessionDataBySessionId("id-1", "data", DateTimeImmutable::createFromFormat(DateTimeInterface::ISO8601_EXPANDED, '2025-03-22T20:00:01+00:00'));
		$sessionsEntityDao->upsertSessionDataBySessionId("id-2", "data", DateTimeImmutable::createFromFormat(DateTimeInterface::ISO8601_EXPANDED, '2025-03-22T20:00:02+00:00'));

		$safeTimestamp = DateTimeImmutable::createFromFormat(DateTimeInterface::ISO8601_EXPANDED, '2025-03-22T20:00:01+00:00');
		$actual = $sessionsEntityDao->deleteOldSessions($safeTimestamp);
		$this->assertSame(1, $actual);
	}

	#endregion
}
