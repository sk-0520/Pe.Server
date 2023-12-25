<?php

declare(strict_types=1);

namespace PeServerUT\Core\Http\Client;

use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use PeServer\Core\Http\Client\StringContent;
use PeServer\Core\Text;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;

class StringContentTest extends TestClass
{
	public function test_default_encoding()
	{
		$sc = new StringContent("abc");
		$this->assertSame("abc", $sc->toBody()->raw);
		$this->assertFalse($sc->toHeader()->existsContentType());
	}

	public static function provider_encoding()
	{
		return [
			['あいうえお', Encoding::getDefaultEncoding()],
			['あいうえお', Encoding::getShiftJis()],
			['あいうえお', Encoding::getUtf8()],
			['あいうえお', Encoding::getUtf16()],
			['あいうえお', Encoding::getUtf32()],
		];
	}

	#[DataProvider('provider_encoding')]
	public function test_encoding(string $input, Encoding $encoding)
	{
		$sc = new StringContent($input, Text::EMPTY, $encoding);

		$this->assertSame($input, $encoding->toString($sc->toBody()));

		if ($encoding->name === Encoding::getDefaultEncoding()->name) {
			$this->assertSame($input, $sc->toBody()->raw);
		} else {
			$this->assertNotSame($input, $sc->toBody()->raw);
		}
	}

	public static function provider_mime()
	{
		return [
			['abc'],
			['text/plain'],
		];
	}

	#[DataProvider('provider_mime')]
	public function test_mime(string $mime)
	{
		$sc = new StringContent(Text::EMPTY, $mime);

		$this->assertSame($mime, $sc->toHeader()->getContentType()->mime);
	}
}
