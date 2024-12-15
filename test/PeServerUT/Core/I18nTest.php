<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\Environment;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\EnvironmentException;
use PeServer\Core\I18n;
use PeServerTest\UtStaticResetTestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class I18nTest extends UtStaticResetTestClass
{
	#region function

	public static function provider_message_key()
	{
		return [
			[
				"value",
				[
					"flat/key" => [
						"*" => "value"
					]
				],
				"flat/key"
			],
			[
				"value",
				[
					"flat" => [
						"key" => [
							"*" => "value"
						]
					]
				],
				"flat/key"
			],
		];
	}

	#[DataProvider('provider_message_key')]
	public function test_message_key(string $expected, array $i18nConfiguration, string $key): void
	{
		try {
			$this->resetInitializeChecker();

			I18n::initialize($i18nConfiguration);
			$actual = I18n::message($key);
			$this->assertSame($expected, $actual);
		} finally {
			$this->restoreInitializeChecker();
		}
	}

	#endregion
}
