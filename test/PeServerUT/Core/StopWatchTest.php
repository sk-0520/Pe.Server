<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServerTest\TestClass;
use PeServer\Core\Stopwatch;

class StopwatchTest extends TestClass
{
	public function test_scenario()
	{
		$stopwatch = Stopwatch::startNew();
		$this->assertTrue($stopwatch->isRunning());
		usleep(100);
		$x = $stopwatch->getElapsed();
		usleep(100);
		$stopwatch->stop();
		$this->assertFalse($stopwatch->isRunning());
		$y = $stopwatch->getElapsed();
		$this->assertLessThan($y, $x);
	}
}
