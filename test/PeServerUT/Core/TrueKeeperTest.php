<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\Throws\MagicPropertyException;
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
		$this->assertTrue($tk->state);
		$this->assertFalse($tk->latest);

		$tk->state = true;
		$this->assertTrue($tk->state);
		$this->assertTrue($tk->latest);

		$tk->state = true;
		$this->assertTrue($tk->state);
		$this->assertTrue($tk->latest);
	}

	public function test_assignTrueFalse()
	{
		$tk = new TrueKeeper();

		$tk->state = true;
		$this->assertTrue($tk->state);
		$this->assertTrue($tk->latest);

		$tk->state = false;
		$this->assertFalse($tk->state);
		$this->assertFalse($tk->latest);
	}

	public function test_assignTrueFalseTrue()
	{
		$tk = new TrueKeeper();

		$tk->state = true;
		$this->assertTrue($tk->state);
		$this->assertTrue($tk->latest);

		$tk->state = false;
		$this->assertFalse($tk->state);
		$this->assertFalse($tk->latest);

		$tk->state = true;
		$this->assertFalse($tk->state);
		$this->assertTrue($tk->latest);
	}

	public function test_get_throw()
	{
		$this->expectException(MagicPropertyException::class);
		$tk = new TrueKeeper();
		$_ = $tk->value;
		$this->fail();
	}

	public function test_set_throw()
	{
		$this->expectException(MagicPropertyException::class);
		$tk = new TrueKeeper();
		$tk->value = true;
		$this->fail();
	}
}
