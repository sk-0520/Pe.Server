<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use DateInterval;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\FormatException;
use PeServer\Core\Time;
use PeServerTest\Data;
use PeServerTest\TestClass;

class TimeTest extends TestClass
{
	public function test_create_ISO8601()
	{
		$tests = [
			new Data('01/00/00 00:00:00', 'P01Y'),
			new Data('2134/00/00 00:00:00', 'P2134Y'),
			new Data('00/01/00 00:00:00', 'P01M'),
			new Data('00/13/00 00:00:00', 'P13M'), //ほんとよくない
			new Data('00/00/01 00:00:00', 'P01D'),
			new Data('00/00/00 01:00:00', 'PT01H'),
			new Data('00/00/00 00:01:00', 'PT01M'),
			new Data('00/00/00 00:00:01', 'PT01S'),
			new Data('01/02/03 04:05:06', 'P01Y02M03DT04H05M06S'),
		];
		foreach ($tests as $test) {
			$actual = Time::create(...$test->args);
			$timestamp = $actual->format('%Y/%M/%D %H:%I:%S');
			$this->assertSame($test->expected, $timestamp);
		}
	}

	public function test_create_Readable()
	{
		$tests = [
			new Data('01/00/00 00:00:00', '365.00:00:00'),
			//new Data('2134/00/00 00:00:00', '778910.00:00:00'), ほんまもう
			new Data('00/01/00 00:00:00', '31.00:00:00'),
			new Data('00/00/00 10:00:00', '10:00:00'),
			new Data('00/00/00 00:10:00', '00:10:00'),
			new Data('00/00/00 00:00:10', '00:00:10'),
		];
		foreach ($tests as $test) {
			$actual = Time::create(...$test->args);
			$timestamp = $actual->format('%Y/%M/%D %H:%I:%S');
			$this->assertSame($test->expected, $timestamp);
		}
	}

	public function test_create_Constructor()
	{
		$tests = [
			new Data('01/00/00 00:00:00', '1 year'),
			//new Data('2134/00/00 00:00:00', '778910.00:00:00'), ほんまもう
		];
		foreach ($tests as $test) {
			$actual = Time::create(...$test->args);
			$timestamp = $actual->format('%Y/%M/%D %H:%I:%S');
			$this->assertSame($test->expected, $timestamp);
		}
	}

	public function provider_create_throw()
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

	/** @dataProvider provider_create_throw */
	public function test_create_throw($input, $exception)
	{
		$this->expectException($exception);
		Time::create($input);
		$this->fail();
	}

	public function test_toString_ISO8601()
	{
		$tests = [
			new Data('P1Y', 'P01Y'),
			new Data('P1Y2M3D', 'P01Y02M03D'),
			new Data('PT1M', 'PT01M'),
			new Data('PT30M', 'PT30M'),
			new Data('PT1H2M3S', 'PT01H02M03S'),
			new Data('P1Y2M3DT1H2M3S', 'P01Y02M03DT01H02M03S'),
		];
		foreach ($tests as $test) {
			$time = new DateInterval($test->args[0]);
			$actual = Time::toString($time, Time::FORMAT_ISO8601);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}

	public function test_toString_Readable()
	{
		$tests = [
			new Data('365.00:00:00', 'P01Y'),
			new Data('428.00:00:00', 'P01Y02M03D'),
			new Data('00:01:00', 'PT01M'),
			new Data('00:30:00', 'PT30M'),
			new Data('01:02:03', 'PT01H02M03S'),
			new Data('428.01:02:03', 'P01Y02M03DT01H02M03S'),
		];
		foreach ($tests as $test) {
			$time = new DateInterval($test->args[0]);
			$actual = Time::toString($time, Time::FORMAT_READABLE);
			$this->assertSame($test->expected, $actual, $test->str());
		}
	}
}
