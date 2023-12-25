<?php

declare(strict_types=1);

namespace PeServerUT\Core\Database;

use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseUtility;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class DatabaseUtilityTest extends TestClass
{
	public static function provider_isSqliteTest()
	{
		return [
			[false, new ConnectionSetting('', '', '')],
			[false, new ConnectionSetting('sqlite', '', '')],
			[false, new ConnectionSetting('sqlite/a.db', '', '')],
			[true, new ConnectionSetting('sqlite:a.db', '', '')],
			[true, new ConnectionSetting('sqlite:a.sqlite3', '', '')],
		];
	}

	#[DataProvider('provider_isSqliteTest')]
	public function test_isSqliteTest(bool $expected, ConnectionSetting $connection)
	{
		$actual = DatabaseUtility::isSqlite($connection);
		$this->assertSame($expected, $actual);
	}

	public static function provider_isSqliteMemoryMode()
	{
		return [
			[false, new ConnectionSetting('sqlite:a.sqlite3', '', '')],
			[false, new ConnectionSetting('sqlite:a.sqlite3?mode=ram', '', '')],
			[true, new ConnectionSetting('sqlite::memory:', '', '')],
			[true, new ConnectionSetting('sqlite:a.sqlite3?mode=memory', '', '')],
			[true, new ConnectionSetting('sqlite:a.sqlite3?a=b&mode=memory', '', '')],
		];
	}

	#[DataProvider('provider_isSqliteMemoryMode')]
	public function test_isSqliteMemoryMode(bool $expected, ConnectionSetting $cs)
	{
		$actual = DatabaseUtility::isSqliteMemoryMode($cs);
		$this->assertSame($expected, $actual);
	}

	public static function provider_isSqliteMemoryMode_throw()
	{
		return [
			[new ConnectionSetting('', '', '')],
			[new ConnectionSetting('sqlite', '', '')],
			[new ConnectionSetting('pgsql:localhost', '', '')],
		];
	}

	#[DataProvider('provider_isSqliteMemoryMode_throw')]
	public function test_isSqliteMemoryMode_throw(ConnectionSetting $cs)
	{
		$this->expectException(InvalidOperationException::class);

		DatabaseUtility::isSqliteMemoryMode($cs);
		$this->fail();
	}


	public static function provider_getSqliteFilePath()
	{
		return [
			['', new ConnectionSetting('sqlite:', '', '')],
			['a.db', new ConnectionSetting('sqlite:a.db', '', '')],
			['/a/b/c.db', new ConnectionSetting('sqlite:/a/b/c.db', '', '')],
		];
	}

	#[DataProvider('provider_getSqliteFilePath')]
	public function test_getSqliteFilePath(string $expected, ConnectionSetting $cs)
	{
		$actual = DatabaseUtility::getSqliteFilePath($cs);
		$this->assertSame($expected, $actual);
	}

	public static function provider_getSqliteFilePath_throw()
	{
		return [
			[ArgumentException::class, new ConnectionSetting('', '', '')],
			[ArgumentException::class, new ConnectionSetting('sqlite', '', '')],
			[ArgumentException::class, new ConnectionSetting('pgsql:localhost', '', '')],
			[InvalidOperationException::class, new ConnectionSetting('sqlite::memory:', '', '')],
			[InvalidOperationException::class, new ConnectionSetting('sqlite:file:a.db?mode=memory', '', '')],
		];
	}

	#[DataProvider('provider_getSqliteFilePath_throw')]
	public function test_getSqliteFilePath_throw(string $expected, ConnectionSetting $cs)
	{
		$this->expectException($expected);

		DatabaseUtility::getSqliteFilePath($cs);
		$this->fail();
	}
}
