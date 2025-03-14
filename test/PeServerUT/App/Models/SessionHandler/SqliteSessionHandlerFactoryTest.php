<?php

declare(strict_types=1);

namespace PeServerUT\App\Models\SessionHandler;

use PeServer\App\Models\SessionHandler\SqliteSessionHandlerFactory;
use PeServer\Core\Store\ISessionHandlerFactory;
use PeServer\Core\Store\SessionOptions;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\TestWith;
use SessionHandlerInterface;

class SqliteSessionHandlerFactoryTest extends TestClass
{
	#region function

	#[TestWith([false, "class"])]
	#[TestWith([false, LocalNotFactory::class])]
	#[TestWith([false, LocalSessionHandlerFactory::class])]
	#[TestWith([true, SqliteSessionHandlerFactory::class])]
	#[TestWith([true, LocalSubSqliteSessionHandlerFactory::class])]
	public function test_isSqliteFactory(bool $expected, string $name)
	{
		$actual = SqliteSessionHandlerFactory::isSqliteFactory($name);
		$this->assertSame($expected, $actual);
	}

	#endregion
}

class LocalNotFactory
{
	//NOP
}

class LocalSessionHandlerFactory implements ISessionHandlerFactory
{
	public function create(SessionOptions $options): SessionHandlerInterface
	{
		assert(false);
	}
}

class LocalSubSqliteSessionHandlerFactory extends SqliteSessionHandlerFactory
{
	//NOP
}
