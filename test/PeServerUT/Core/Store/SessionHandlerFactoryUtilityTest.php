<?php

declare(strict_types=1);

namespace PeServerUT\Core\Store;

use PeServer\Core\Store\ISessionHandlerFactory;
use PeServer\Core\Store\SessionHandlerFactoryUtility;
use PeServer\Core\Store\SessionOptions;
use PeServerTest\TestClass;
use PHPStan\BetterReflection\Reflection\Adapter\Exception\NotImplemented;
use PHPUnit\Framework\Attributes\DataProvider;
use SessionHandlerInterface;

class SessionHandlerFactoryUtilityTest extends TestClass
{
	#region function

	public static function provider_isFactory()
	{
		return [
			[false, null],
			[false, ""],
			[false, " "],
			[false, LocalFalseClass::class],
			[true, LocalImplClass::class],
		];
	}

	#[DataProvider('provider_isFactory')]
	public function test_isFactory(bool $expected, ?string $name)
	{
		$actual = SessionHandlerFactoryUtility::isFactory($name);
		$this->assertSame($expected, $actual);
	}

	#endregion
}


class LocalFalseClass
{
	//NOP
}

class LocalImplClass implements ISessionHandlerFactory
{
	public function create(SessionOptions $options): SessionHandlerInterface
	{
		throw new NotImplemented();
	}
}
