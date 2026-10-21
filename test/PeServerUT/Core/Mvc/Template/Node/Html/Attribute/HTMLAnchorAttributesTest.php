<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Template\Node\Html\Attribute;

use PeServer\Core\Mvc\Template\Node\Attribute\AttributeValueMark;
use PeServer\Core\Mvc\Template\Node\Html\Attribute\HTMLAnchorAttributes;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class HTMLAnchorAttributesTest extends TestClass
{
	public static function provide_download(): array
	{
		return [
			['download', true],
			['', false],
		];
	}

	/**
	 * @dataProvider provide_download
	 */
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
}
