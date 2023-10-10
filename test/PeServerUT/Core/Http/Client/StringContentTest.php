<?php

declare(strict_types=1);

namespace PeServerUT\Core\Http\Client;

use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use PeServer\Core\Http\Client\StringContent;
use PeServer\Core\Text;
use PeServerTest\Data;
use PeServerTest\TestClass;

class StringContentTest extends TestClass
{
	public function test_default_encoding()
	{
		$sc = new StringContent("abc");
		$this->assertSame("abc", $sc->toBody()->raw);
		$this->assertFalse($sc->toHeader()->existsContentType());
	}

	public function test_encoding()
	{
		$tests = [
			['input' => 'あいうえお', 'encoding' => Encoding::getDefaultEncoding()],
			['input' => 'あいうえお', 'encoding' => Encoding::getShiftJis()],
			['input' => 'あいうえお', 'encoding' => Encoding::getUtf8()],
			['input' => 'あいうえお', 'encoding' => Encoding::getUtf16()],
			['input' => 'あいうえお', 'encoding' => Encoding::getUtf32()],
		];
		foreach ($tests as $test) {
			$sc = new StringContent($test['input'], Text::EMPTY, $test['encoding']);

			$this->assertSame($test['input'], $test['encoding']->toString($sc->toBody()));

			if ($test['encoding']->name === Encoding::getDefaultEncoding()->name) {
				$this->assertSame($test['input'], $sc->toBody()->raw);
			} else {
				$this->assertNotSame($test['input'], $sc->toBody()->raw);
			}
		}
	}

	public function test_mime()
	{
		$tests = [
			'abc',
			'text/plain'
		];
		foreach ($tests as $test) {
			$sc = new StringContent(Text::EMPTY, $test);

			$this->assertSame($test, $sc->toHeader()->getContentType()->mime);
		}
	}
}
