<?php

declare(strict_types=1);

namespace PeServerUT\Core\Migration;

use PeServer\Core\Log\LoggerFactory;
use PeServer\Core\Migration\MigrationArgument;
use PeServer\Core\Migration\MigrationBase;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class MigrationBaseTest extends TestClass
{
	#region function

	public static function provider_splitStatements()
	{
		return [
			[
				[],
				""
			],
			[
				[],
				";"
			],
			[
				["a"],
				"a"
			],
			[
				[],
				<<<SQL
				;
				;
				SQL
			],
			[
				[
					"a",
					"b",
				],
				<<<SQL
				a
				;
				b
				;
				SQL
			],
			[
				[
					"a",
					"b",
					"c",
				],
				<<<SQL
				a
				;
				b
				;
				c
				SQL
			],
			[
				[
					<<<DATA
					a
					<;
					b
					;>
					c
					DATA
				],
				<<<SQL
				a
				<;
				b
				;>
				c
				SQL
			],		];
	}

	#[DataProvider('provider_splitStatements')]
	public function test_splitStatements(array $expected, string $input)
	{
		$obj = new class extends MigrationBase {
			public function __construct()
			{
				parent::__construct(0, LoggerFactory::createNullFactory());
			}
			public function migrate(MigrationArgument $argument): void
			{
				assert(false);
			}
		};

		$actual = $this->callInstanceMethod($obj, "splitStatements", [$input]);
		$this->assertSame($expected, $actual);
	}

	#endregion
}
