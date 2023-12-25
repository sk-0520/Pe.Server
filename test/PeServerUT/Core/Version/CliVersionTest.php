<?php

declare(strict_types=1);

namespace PeServerUT\Core\Version;

use PeServerTest\TestClass;
use PeServer\Core\Version\CliVersion;
use PHPUnit\Framework\Attributes\DataProvider;

class CliVersionTest extends TestClass
{
	public static function provider_toString()
	{
		return [
			['1.0.0', 1],
			['1.2.0', 1, 2],
			['1.2.3', 1, 2, 3],
			['1.2.3.4', 1, 2, 3, 4],
			['1.2.3', 1, 2, 3, -1],
		];
	}

	#[DataProvider('provider_toString')]
	public function test_toString(string $expected, int $major, int $minor = 0, int $build = 0, int $revision = CliVersion::IGNORE_REVISION)
	{
		$actual = new CliVersion($major, $minor, $build, $revision);
		$this->assertSame($expected, $actual->toString());
		$this->assertSame($expected, (string)$actual);
		$this->assertSame($expected, strval($actual));
	}

	public static function provider_tryParse_success()
	{
		return [
			['1.0.0', '1'],
			['1.0.0', '01'],
			['10.0.0', '10'],
			['1.2.0', '1.2'],
			['1.2.0', '1.02'],
			['1.20.0', '1.20'],
			['1.2.3', '1.2.3'],
			['1.2.3', '1.2.03'],
			['1.2.30', '1.2.30'],
			['1.2.3.4', '1.2.3.4'],
			['1.2.3.4', '1.2.3.04'],
			['1.2.3.40', '1.2.3.40'],
		];
	}

	#[DataProvider('provider_tryParse_success')]
	public function test_tryParse_success(string $expected, string $s)
	{
		$actual = CliVersion::tryParse($s, $result);
		$this->assertTrue($actual);
		$this->assertSame($expected, $result->toString());
		$this->assertSame($expected, (string)$result);
		$this->assertSame($expected, strval($result));
	}

	public static function provider_tryParse_failure()
	{
		return [
			[null],
			[''],
			['1a'],
			['a1'],
			['a'],
			['1.'],
			['1.a'],
			['a.2'],
			['1.2.'],
			['1.2.a'],
			['1.a.3'],
		];
	}

	#[DataProvider('provider_tryParse_failure')]
	public function test_tryParse_failure(?string $input)
	{
		$actual = CliVersion::tryParse($input, $_);
		$this->assertFalse($actual);
	}
}
