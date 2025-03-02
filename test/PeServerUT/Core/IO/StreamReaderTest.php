<?php

declare(strict_types=1);

namespace PeServerUT\Core\IO;

use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use PeServerTest\TestClass;
use PeServer\Core\IO\Stream;
use PeServer\Core\IO\StreamReader;
use PHPUnit\Framework\Attributes\DataProvider;

class StreamReaderTest extends TestClass
{
	#region function

	public static function provider_readBom()
	{
		return [
			[false, new Binary(''), 0, Encoding::getAscii()],
			[false, new Binary('A'), 1, Encoding::getAscii()],
			[false, new Binary("\xEF"), 0, Encoding::getUtf8()],
			[false, new Binary("\xEF\xBB"), 0, Encoding::getUtf8()],
			[true, new Binary("\xEF\xBB\xBF"), 0, Encoding::getUtf8()],
			[true, new Binary("\xFE\xFF"), 0, new Encoding(Encoding::ENCODE_UTF16_BE)],
			[false, new Binary("\xFF\xFE"), 0, new Encoding(Encoding::ENCODE_UTF16_BE)],
			[true, new Binary("\xFF\xFE"), 0, new Encoding(Encoding::ENCODE_UTF16_LE)],
			[false, new Binary("\xFE\xFF"), 0, new Encoding(Encoding::ENCODE_UTF16_LE)],
			[true, new Binary("\x00\x00\xFE\xFF"), 0, new Encoding(Encoding::ENCODE_UTF32_BE)],
			[false, new Binary("\xFF\xFE\x00\x00"), 0, new Encoding(Encoding::ENCODE_UTF32_BE)],
			[true, new Binary("\xFF\xFE\x00\x00"), 0, new Encoding(Encoding::ENCODE_UTF32_LE)],
			[false, new Binary("\x00\x00\xFE\xFF"), 0, new Encoding(Encoding::ENCODE_UTF32_LE)],
		];
	}

	#[DataProvider('provider_readBom')]
	public function test_readBom(bool $expected, Binary $init, int $start, Encoding $encoding)
	{
		$stream = Stream::openTemporary(encoding: $encoding);
		$reader = new StreamReader($stream, $encoding);

		if ($init->count()) {
			$stream->writeBinary($init);
		}
		$stream->seek($start, Stream::WHENCE_HEAD);

		$actual = $reader->readBom();

		$this->assertSame($expected, $actual);
	}

	#endregion
}
