<?php

declare(strict_types=1);

namespace PeServerUT\Core\Database;

use PeServer\Core\Database\ConnectionSetting;
use PeServer\Core\Database\DatabaseUtility;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServerTest\Data;
use PeServerTest\TestClass;

class DatabaseUtilityTest extends TestClass
{
	public function test_isSqliteTest()
	{
		$tests = [
			new Data(false, new ConnectionSetting('', '', '')),
			new Data(false, new ConnectionSetting('sqlite', '', '')),
			new Data(false, new ConnectionSetting('sqlite/a.db', '', '')),
			new Data(true, new ConnectionSetting('sqlite:a.db', '', '')),
			new Data(true, new ConnectionSetting('sqlite:a.sqlite3', '', '')),
		];
		foreach ($tests as $test) {
			$actual = DatabaseUtility::isSqlite(...$test->args);
			$this->assertSame($test->expected, $actual, $test->str());
		}
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

	/** @dataProvider provider_isSqliteMemoryMode */
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

	/** @dataProvider provider_isSqliteMemoryMode_throw */
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

	/** @dataProvider provider_getSqliteFilePath */
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

	/** @dataProvider provider_getSqliteFilePath_throw */
	public function test_getSqliteFilePath_throw(string $expected, ConnectionSetting $cs)
	{
		$this->expectException($expected);

		DatabaseUtility::getSqliteFilePath($cs);
		$this->fail();
	}
}
