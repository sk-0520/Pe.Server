<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServerTest\Data;
use PeServerTest\TestClass;
use PeServer\Core\Timer;

class TimerTest extends TestClass
{
	function test_scenario()
	{
		$timer = Timer::startNew();
		$this->assertTrue($timer->isRunning());
		usleep(100);
		$x = $timer->getElapsed();
		usleep(100);
		$timer->stop();
		$this->assertFalse($timer->isRunning());
		$y = $timer->getElapsed();
		$this->assertLessThan($y,$x);
	}
}
