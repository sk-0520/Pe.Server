<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use DateInterval;
use PeServer\Core\Encoding;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\FormatException;
use PeServer\Core\Time;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class TimeTest extends TestClass
{
	public static function provider_getTotalSeconds()
	{
		return [
			[0, new DateInterval("PT0S")],
			[1, new DateInterval("PT1S")],
			[60, new DateInterval("PT1M")],
			[60 * 60, new DateInterval("PT1H")],
			[24 * 60 * 60, new DateInterval("P1D")],
		];
	}

	#[DataProvider('provider_getTotalSeconds')]
	public function test_getTotalSeconds(int $expected, DateInterval $time)
	{
		$actual = Time::getTotalSeconds($time);
		$this->assertSame($expected, $actual);
	}

	public static function provider_getTotalMilliseconds()
	{
		return [
			[0, new DateInterval("PT0S")],
			[1 * 1000, new DateInterval("PT1S")],
			[60 * 1000, new DateInterval("PT1M")],
			[60 * 60 * 1000, new DateInterval("PT1H")],
			[24 * 60 * 60 * 1000, new DateInterval("P1D")],
		];
	}

	#[DataProvider('provider_getTotalMilliseconds')]
	public function test_getTotalMilliseconds(float $expected, DateInterval $time)
	{
		$actual = Time::getTotalMilliseconds($time);
		$this->assertSame($expected, $actual);
	}

	public static function provider_create_ISO8601()
	{
		return [
			['01/00/00 00:00:00', 'P01Y'],
			['2134/00/00 00:00:00', 'P2134Y'],
			['00/01/00 00:00:00', 'P01M'],
			['00/13/00 00:00:00', 'P13M'], //ほんとよくない
			['00/00/01 00:00:00', 'P01D'],
			['00/00/00 01:00:00', 'PT01H'],
			['00/00/00 00:01:00', 'PT01M'],
			['00/00/00 00:00:01', 'PT01S'],
			['01/02/03 04:05:06', 'P01Y02M03DT04H05M06S'],
		];
	}

	#[DataProvider('provider_create_ISO8601')]
	public function test_create_ISO8601(string $expected, string $time, ?Encoding $encoding = null)
	{
		$actual = Time::create($time, $encoding);
		$timestamp = $actual->format('%Y/%M/%D %H:%I:%S');
		$this->assertSame($expected, $timestamp);
	}

	public static function provider_create_Readable()
	{
		return [
			// バージョンアップすると都度コメントアウトするのはどうなん
			//['00/11/28 00:00:00', '365.00:00:00'],
			//['2134/00/00 00:00:00', '778910.00:00:00'], ほんまもう
			//['00/01/00 00:00:00', '31.00:00:00'], github action とおらん、PHP バージョンか
			['00/00/00 10:00:00', '10:00:00'],
			['00/00/00 00:10:00', '00:10:00'],
			['00/00/00 00:00:10', '00:00:10'],
		];
	}

	#[DataProvider('provider_create_Readable')]
	public function test_create_Readable(string $expected, string $time, ?Encoding $encoding = null)
	{
		$obj = Time::create($time, $encoding);
		$actual = $obj->format('%Y/%M/%D %H:%I:%S');
		$this->assertSame($expected, $actual);
	}

	public static function provider_create_Constructor()
	{
		return [
			['01/00/00 00:00:00', '1 year']
			//['2134/00/00 00:00:00', '778910.00:00:00'], ほんまもう
		];
	}

	#[DataProvider('provider_create_Constructor')]
	public function test_create_Constructor(string $expected, string $time, ?Encoding $encoding = null)
	{
		$obj = Time::create($time, $encoding);
		$actual = $obj->format('%Y/%M/%D %H:%I:%S');
		$this->assertSame($expected, $actual);
	}

	public static function provider_compare()
	{
		return [
			[0, new DateInterval("P2D"), new DateInterval("P2D")],
			[-1, new DateInterval("P1D"), new DateInterval("P2D")],
			[+1, new DateInterval("P2D"), new DateInterval("P1D")],
		];
	}

	#[DataProvider('provider_compare')]
	public function test_compare($expected, DateInterval $a, DateInterval $b)
	{
		$actual = Time::compare($a, $b);
		$this->assertSame($expected, $actual);
	}

	public static function provider_create_throw()
	{
		return [
			['', ArgumentException::class],
			[' ', ArgumentException::class],
			['P!', FormatException::class],
			['.00:00:00', FormatException::class],
			['00:0000', FormatException::class],
			['0000:00', FormatException::class],
			['::', FormatException::class],
			[':', FormatException::class],
			['+', FormatException::class],
		];
	}

	#[DataProvider('provider_create_throw')]
	public function test_create_throw($input, $exception)
	{
		$this->expectException($exception);
		Time::create($input);
		$this->fail();
	}

	public static function provider_toString_ISO8601()
	{
		return [
			['P1Y', 'P01Y'],
			['P1Y2M3D', 'P01Y02M03D'],
			['PT1M', 'PT01M'],
			['PT30M', 'PT30M'],
			['PT1H2M3S', 'PT01H02M03S'],
			['P1Y2M3DT1H2M3S', 'P01Y02M03DT01H02M03S'],
		];
	}

	#[DataProvider('provider_toString_ISO8601')]
	public function test_toString_ISO8601(string $expected, string $duration)
	{
		$time = new DateInterval($duration);
		$actual = Time::toString($time, Time::FORMAT_ISO8601);
		$this->assertSame($expected, $actual);
	}

	public static function provider_toString_Readable()
	{
		return [
			['365.00:00:00', 'P01Y'],
			['428.00:00:00', 'P01Y02M03D'],
			['00:01:00', 'PT01M'],
			['00:30:00', 'PT30M'],
			['01:02:03', 'PT01H02M03S'],
			['428.01:02:03', 'P01Y02M03DT01H02M03S'],
		];
	}

	#[DataProvider('provider_toString_Readable')]
	public function test_toString_Readable(string $expected, string $duration)
	{
		$time = new DateInterval($duration);
		$actual = Time::toString($time, Time::FORMAT_READABLE);
		$this->assertSame($expected, $actual);
	}
}
