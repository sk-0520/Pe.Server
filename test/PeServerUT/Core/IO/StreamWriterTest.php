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

	public function test_constructor_leaveOpen_default()
	{
		$stream = Stream::openTemporary(encoding: Encoding::getDefaultEncoding());
		$writer = new StreamWriter($stream, Encoding::getDefaultEncoding());

		$writer->dispose();

		$this->assertTrue($writer->isDisposed());
		$this->assertTrue($stream->isDisposed());
	}

	public function test_constructor_leaveOpen_close()
	{
		$stream = Stream::openTemporary(encoding: Encoding::getDefaultEncoding());
		$writer = new StreamWriter($stream, Encoding::getDefaultEncoding(), false);

		$writer->dispose();

		$this->assertTrue($writer->isDisposed());
		$this->assertTrue($stream->isDisposed());
	}

	public function test_constructor_leaveOpen_open()
	{
		$stream = Stream::openTemporary(encoding: Encoding::getDefaultEncoding());
		$writer = new StreamWriter($stream, Encoding::getDefaultEncoding(), true);

		$writer->dispose();

		$this->assertTrue($writer->isDisposed());
		$this->assertFalse($stream->isDisposed());
	}

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

	public static function provider_writeString()
	{
		return [
			[0, "", Encoding::getAscii()],
			[0, "", Encoding::getUtf8()],
			[0, "", Encoding::getUtf16()],
			[0, "", Encoding::getUtf32()],

			[3, "abc", Encoding::getAscii()],
			[3, "abc", Encoding::getUtf8()],
			[6, "abc", Encoding::getUtf16()],
			[12, "abc", Encoding::getUtf32()],

			[4, "🐎", Encoding::getUtf8()],
			[4, "🐎", Encoding::getUtf16()],
			[4, "🐎", Encoding::getUtf32()],
		];
	}

	#[DataProvider('provider_writeString')]
	public function test_writeString(int $expected, string $s, Encoding $encoding)
	{
		$stream = Stream::openTemporary(encoding: $encoding);
		$writer = new StreamWriter($stream, $encoding);

		$actualLength = $writer->writeString($s);
		$this->assertSame($expected, $actualLength);

		$stream->seekHead();
		$actualContent = $stream->readBinaryContents();
		$this->assertSame($encoding->getBinary($s)->raw, $actualContent->raw);
	}

	public function test_writeString_ascii_emoji()
	{
		$encoding = Encoding::getAscii();
		$stream = Stream::openTemporary(encoding: $encoding);
		$writer = new StreamWriter($stream, $encoding);

		$actualLength = $writer->writeString("🐎");
		$this->assertSame(1, $actualLength);

		$stream->seekHead();
		$actualContent = $stream->readBinaryContents();
		// 変換できなかった場合に ? にするより落とした方が安全な気もせんでもないが、それは Encoding の責務ということで。
		$this->assertSame($encoding->getBinary("?")->raw, $actualContent->raw);
	}

	public static function provider_writeLine()
	{
		return [
			[0 + 1, "", "\r", Encoding::getAscii()],
			[0 + 1, "", "\n", Encoding::getAscii()],
			[0 + 2, "", "\r\n", Encoding::getAscii()],
			[0 + 1, "", "\r", Encoding::getUtf8()],
			[0 + 1, "", "\n", Encoding::getUtf8()],
			[0 + 2, "", "\r\n", Encoding::getUtf8()],
			[0 + 2, "", "\r", Encoding::getUtf16()],
			[0 + 2, "", "\n", Encoding::getUtf16()],
			[0 + 4, "", "\r\n", Encoding::getUtf16()],
			[0 + 4, "", "\r", Encoding::getUtf32()],
			[0 + 4, "", "\n", Encoding::getUtf32()],
			[0 + 8, "", "\r\n", Encoding::getUtf32()],

			[3 + 1, "abc", "\r", Encoding::getAscii()],
			[3 + 1, "abc", "\n", Encoding::getAscii()],
			[3 + 2, "abc", "\r\n", Encoding::getAscii()],
			[3 + 1, "abc", "\r", Encoding::getUtf8()],
			[3 + 1, "abc", "\n", Encoding::getUtf8()],
			[3 + 2, "abc", "\r\n", Encoding::getUtf8()],
			[6 + 2, "abc", "\r", Encoding::getUtf16()],
			[6 + 2, "abc", "\n", Encoding::getUtf16()],
			[6 + 4, "abc", "\r\n", Encoding::getUtf16()],
			[12 + 4, "abc", "\r", Encoding::getUtf32()],
			[12 + 4, "abc", "\n", Encoding::getUtf32()],
			[12 + 8, "abc", "\r\n", Encoding::getUtf32()],
		];
	}

	#[DataProvider('provider_writeLine')]
	public function test_writeLine(int $expected, string $s, string $newLine, Encoding $encoding)
	{
		$stream = Stream::openTemporary(encoding: $encoding);
		$writer = new StreamWriter($stream, $encoding);
		$writer->newLine = $newLine;

		$actualLength = $writer->writeLine($s);
		$this->assertSame($expected, $actualLength);

		$stream->seekHead();
		$actualContent = $stream->readBinaryContents();
		$this->assertSame($encoding->getBinary($s . $newLine)->raw, $actualContent->raw);
	}



	#endregion
}
