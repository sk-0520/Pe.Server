<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\TrueKeeper;
use PeServerTest\TestClass;

class TrueKeeperTest extends TestClass
{
	public function test_new()
	{
		$tk = new TrueKeeper();
		$this->assertTrue($tk->state);
	}

	public function test_assignTrueTrue()
	{
		$tk = new TrueKeeper();

		$tk->state = true;
		$tk->state = true;

		$this->assertTrue($tk->state);
	}

	public function test_assignTrueFalse()
	{
		$tk = new TrueKeeper();

		$tk->state = true;
		$tk->state = false;

		$this->assertFalse($tk->state);
	}

	public function test_assignTrueFalseTrue()
	{
		$tk = new TrueKeeper();

		$tk->state = true;
		$tk->state = false;
		$tk->state = true;

		$this->assertFalse($tk->state);
	}

	public function test_throw()
	{
		$this->expectException(ArgumentException::class);
		$tk = new TrueKeeper();
		$tk->value = true;
		$this->fail();
	}
}
