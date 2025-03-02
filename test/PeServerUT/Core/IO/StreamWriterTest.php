<?php

declare(strict_types=1);

namespace PeServerUT\Core\IO;

use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use PeServerTest\TestClass;
use PeServer\Core\IO\Stream;
use PeServer\Core\IO\StreamWriter;
use PHPUnit\Framework\Attributes\DataProvider;

class StreamWriterTest extends TestClass
{
	#region function

	public static function provider_writeBom()
	{
		return [
			[0, new Binary(''), Encoding::getAscii()],
			[0, new Binary('data'), Encoding::getAscii()],
			[0, new Binary(''), Encoding::getShiftJis()],
			[0, new Binary('data'), Encoding::getShiftJis()],
			[3, new Binary(''), Encoding::getUtf8()],
			[0, new Binary('data'), Encoding::getUtf8()],
			[2, new Binary(''), Encoding::getUtf16()],
			[0, new Binary('data'), Encoding::getUtf16()],
			[4, new Binary(''), Encoding::getUtf32()],
			[0, new Binary('data'), Encoding::getUtf32()],
		];
	}

	#[DataProvider('provider_writeBom')]
	public function test_writeBom(int $expected, Binary $init, Encoding $encoding)
	{
		$stream = Stream::openTemporary(encoding: $encoding);
		$writer = new StreamWriter($stream, $encoding);

		if ($init->count()) {
			$stream->writeBinary($init);
		}
		$actual = $writer->writeBom();

		$this->assertSame($expected, $actual);
	}

	#endregion
}
