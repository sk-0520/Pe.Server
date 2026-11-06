<?php

declare(strict_types=1);

namespace PeServerUT\Core\DI;

use PeServer\Core\DI\DiRegisterContainer;
use PeServer\Core\DI\ScopedDiContainer;
use PeServer\Core\DisposerBase;
use PeServer\Core\IDisposable;
use PeServerTest\TestClass;

class ScopedDiContainerTest extends TestClass
{
	public function test_clone()
	{
		$dc = new DiRegisterContainer();

		$dc->registerMapping(LocalScopedDiI1::class, LocalScopedDiC1::class);
		$actual1 = $dc->get(LocalScopedDiI1::class);

		$sc = new ScopedDiContainer($dc);
		$actual2 = $sc->get(LocalScopedDiI1::class);

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

interface LocalScopedDiI1
{
	//NOP
}

class LocalScopedDiC1 implements LocalScopedDiI1
{
	//NOP
}

class LocalScopedDiC12 implements LocalScopedDiI1
{
	//NOP
}

class LocalScopedDiC2
{
	public function __construct(
		public LocalScopedDiI1 $i
	) {
		//NOP
	}
}

interface LocalScopedDiC3
{
	//NOP
}

class LocalScopedDiC31
{
	public function __construct(
		public LocalScopedDiI1 $i
	) {
		//NOP
	}
}

class LocalScopedDiC32
{
	public function __construct(
		public LocalScopedDiI1 $i
	) {
		//NOP
	}
}
