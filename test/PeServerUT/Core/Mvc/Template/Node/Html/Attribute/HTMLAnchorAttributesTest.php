<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Attribute\AttributeValueMark;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\StringValues;

class HTMLAnchorAttributesTest extends TestClass
{
	public static function provide_download(): array
	{
		return [
			['download', true],
			['', false],
		];
	}

	#[DataProvider('provide_download')]
	public function test_download(string $expected, bool $download): void
	{
		$test = new HTMLAnchorAttributes(download: $download);
		$actual = $test->build('download', AttributeValueMark::DoubleQuote);
		$this->assertSame($expected, $actual);
	}

	public function test_interestForeign(): void
	{
		$test = new HTMLAnchorAttributes(interestForeign: "abc");
		$actual = $test->build('interestForeign', AttributeValueMark::DoubleQuote);
		$this->assertSame("interestforeign=\"abc\"", $actual);
	}

	public static function provide_ping(): array
	{
		return [
			['ping="abc"', 'abc'],
			['ping="abc def"', 'abc def'],
			['ping="abc"', new StringValues('abc')],
			['ping="abc def ghi"', new StringValues(['abc', 'def', 'ghi'])],
		];
	}

	#[DataProvider('provide_ping')]
	public function test_ping(string $expected, string|StringValues $input): void
	{
		$test = new HTMLAnchorAttributes(ping: $input);
		$actual = $test->build('ping', AttributeValueMark::DoubleQuote);
		$this->assertSame($expected, $actual);
	}
}
