<?php

declare(strict_types=1);

namespace PeServerUT\Core\DI;

use PeServer\Core\DI\DiRegisterContainer;
use PeServer\Core\DI\ScopedDiContainer;
use PeServer\Core\DisposerBase;
use PeServer\Core\IDisposable;
use PeServerUT\TestClass;

class ScopedDiContainerTest extends TestClass
{
	public function test_clone()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(SD_I_1::class, SD_C_1::class);
		$actual1 = $dc->get(SD_I_1::class);

		$sc = new ScopedDiContainer($dc);
		$actual2 = $sc->get(SD_I_1::class);

		$this->assertSame($actual1::class, $actual2::class);
	}

	public function test_value()
	{
		$data = DisposerBase::empty();

		$dc = new DiRegisterContainer();

		$dc->registerValue($data, IDisposable::class);
		$sc = $dc->clone();

		$actual1 = $dc->get(IDisposable::class);
		$actual2 = $sc->get(IDisposable::class);
		$this->assertSame($data, $actual1);
		$this->assertSame($actual1, $actual2);

		$sc->dispose();
		$this->assertFalse($data->isDisposed());

		$dc->dispose();
		$this->assertTrue($data->isDisposed());
	}
}

interface SD_I_1
{
}

class SD_C_1 implements SD_I_1
{
}

class SD_C_1_2 implements SD_I_1
{
}

class SD_C_2
{
	public function __construct(
		public SD_I_1 $i
	) {
	}
}

interface SD_C_3
{
}

class SD_C_3_1
{
	public function __construct(
		public SD_I_1 $i
	) {
	}
}

class SD_C_3_2
{
	public function __construct(
		public SD_I_1 $i
	) {
	}
}
