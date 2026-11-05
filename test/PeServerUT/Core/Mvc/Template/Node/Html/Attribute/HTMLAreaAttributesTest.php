<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Attribute\AttributeValueMark;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAreaAttributes;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\StringValues;
use PeServer\Core\Image\Point;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\PointValues;

class HTMLAreaAttributesTest extends TestClass
{
	public static function provide_coords(): array
	{
		return [
			['coords=""', ""],
			['coords="a"', "a"],
			['coords="1,2"', "1,2"],
			['coords="1,2"', new PointValues(new Point(1, 2))],
			['coords="1,2,3,4"', new PointValues([new Point(1, 2), new Point(3, 4)])],
		];
	}

	#[DataProvider('provide_coords')]
	public function test_coords(string $expected, string|PointValues $coords): void
	{
		$test = new HTMLAreaAttributes(coords: $coords);
		$actual = $test->build('coords', AttributeValueMark::DoubleQuote);
		$this->assertSame($expected, $actual);
	}
}
