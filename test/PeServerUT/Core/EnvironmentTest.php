<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\Environment;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\EnvironmentException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class EnvironmentTest extends TestClass
{
	private function createEnvironment(
		string $locale = 'C',
		string $language = 'uni',
		string $timezone = 'UTC',
		string $environment = 'test',
		string $revision = '$REV$'
	) {
		return new Environment($locale, $language, $timezone, $environment, $revision);
	}

	public function test_env()
	{
		$env = $this->createEnvironment();
		$this->assertSame('test', $env->get());
		$this->assertTrue($env->is('test'));
		$this->assertFalse($env->isDevelopment());
		$this->assertFalse($env->isProduction());
		$this->assertTrue($env->isTest());
	}

	public function test_getRevision()
	{
		$envDevelopment = $this->createEnvironment(environment: 'development');
		$this->assertNotSame('$REV$', $envDevelopment->getRevision());

		$envProduction = $this->createEnvironment(environment: 'production');
		$this->assertSame('$REV$', $envProduction->getRevision());

		$envTest = $this->createEnvironment(environment: 'test');
		$this->assertNotSame('$REV$', $envTest->getRevision());
	}

	public function test_variable()
	{
		$name = 'PeServer_Test_Variable';

		$this->assertNull(Environment::getVariable($name), "$name はテスト用に使うので空けておいてほしいのです");

		Environment::setVariable($name, 'VALUE');
		$this->assertSame('VALUE', Environment::getVariable($name));

		Environment::setVariable($name, '');
		$this->assertSame('', Environment::getVariable($name));

		Environment::setVariable($name, ' ');
		$this->assertSame(' ', Environment::getVariable($name));
	}

	public static function provider_getVariable_throw()
	{
		return [
			[''],
			[' '],
		];
	}

	#[DataProvider('provider_getVariable_throw')]
	public function test_getVariable_throw($input)
	{
		$this->expectException(ArgumentException::class);

		Environment::getVariable($input);
		$this->fail();
	}

	public static function provider_setVariable_throw()
	{
		return [
			[['exception' => ArgumentException::class, 'message' => ''], '', ''],
			[['exception' => ArgumentException::class, 'message' => ' '], ' ', ''],
			[['exception' => EnvironmentException::class, 'message' => '$name: ='], '=', ''],
			[['exception' => EnvironmentException::class, 'message' => '$name: A='], 'A=', ''],
			[['exception' => EnvironmentException::class, 'message' => '$name: =A'], '=A', ''],
		];
	}

	#[DataProvider('provider_setVariable_throw')]
	public function test_setVariable_throw($expected, $name, $value)
	{
		$this->expectException($expected['exception']);
		$this->expectExceptionMessage($expected['message']);

		Environment::setVariable($name, $value);
		Environment::getVariable($name);
		$this->fail();
	}
}
