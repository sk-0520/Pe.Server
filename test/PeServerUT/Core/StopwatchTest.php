<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServerTest\TestClass;
use PeServer\Core\Stopwatch;
use PHPUnit\Framework\Attributes\TestWith;

class StopwatchTest extends TestClass
{
	#[TestWith([0.0, 0])]
	#[TestWith([0.000000001, 1])]
	#[TestWith([0.000001, 1000])]
	#[TestWith([0.001, 1000000])]
	#[TestWith([1.0, 1000000000])]
	#[TestWith([2.0, 2000000000])]
	public function test_nanosecondsToFloat($exception, int $nanoseconds)
	{
		$actual = Stopwatch::nanosecondsToFloat($nanoseconds);
		$this->assertSame($exception, $actual);
	}

	public function test_scenario_1()
	{
		$stopwatch = Stopwatch::startNew();
		$this->assertTrue($stopwatch->isRunning());
		usleep(100);
		$x = $stopwatch->getNanosecondsElapsed();
		usleep(100);
		$stopwatch->stop();
		$this->assertFalse($stopwatch->isRunning());
		$y = $stopwatch->getNanosecondsElapsed();
		$this->assertLessThan($y, $x);
	}

	public function test_scenario_2()
	{
		$stopwatch = Stopwatch::startNew();
		$this->assertTrue($stopwatch->isRunning());
		usleep(100);
		$x = $stopwatch->getElapsed();
		usleep(100);
		$stopwatch->stop();
		$this->assertFalse($stopwatch->isRunning());
		$y = $stopwatch->getElapsed();

		$now = new \DateTimeImmutable();

		$this->assertLessThan($now->add($y), $now->add($x));
	}
}
