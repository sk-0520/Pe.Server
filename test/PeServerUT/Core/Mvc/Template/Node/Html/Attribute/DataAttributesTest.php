<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\DataAttributes;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PeServer\Core\Mvc\Template\Node\AttributeValueMark;

class DataAttributesTest extends TestClass
{
	public static function provider_getNames()
	{
		return [
			[
				[],
				new DataAttributes(),
			],
			[
				[],
				new DataAttributes([]),
			],
			[
				['a'],
				new DataAttributes(["a" => "A"]),
			],
			[
				['a', "b"],
				new DataAttributes(["a" => "A", "b" => "B"]),
			],
		];
	}

	#[DataProvider('provider_getNames')]
	public function test_getNames(array $expected, DataAttributes $test): void
	{
		$actual = iterator_to_array($test->getNames());
		$this->assertSame($expected, $actual);
	}

	public static function provider_build()
	{
		return [
			[
				'data-a="A"',
				new DataAttributes(["a" => "A"]),
				"a",
				AttributeValueMark::DoubleQuote,
			],
			[
				"data-a='A'",
				new DataAttributes(["a" => "A"]),
				"a",
				AttributeValueMark::SingleQuote,
			],
		];
	}

	#[DataProvider('provider_build')]
	public function test_build(string $expected, DataAttributes $test, string $name, AttributeValueMark $mark): void
	{
		$actual = $test->build($name, $mark);
		$this->assertSame($expected, $actual);
	}
}
