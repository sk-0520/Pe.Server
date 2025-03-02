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

	public function test_constructor_leaveOpen_default()
	{
		$stream = Stream::openTemporary();
		$reader = new StreamReader($stream, Encoding::getDefaultEncoding());

		$reader->dispose();

		$this->assertTrue($reader->isDisposed());
		$this->assertTrue($stream->isDisposed());
	}

	public function test_constructor_leaveOpen_close()
	{
		$stream = Stream::openTemporary();
		$reader = new StreamReader($stream, Encoding::getDefaultEncoding(), false);

		$reader->dispose();

		$this->assertTrue($reader->isDisposed());
		$this->assertTrue($stream->isDisposed());
	}

	public function test_constructor_leaveOpen_open()
	{
		$stream = Stream::openTemporary();
		$reader = new StreamReader($stream, Encoding::getDefaultEncoding(), true);

		$reader->dispose();

		$this->assertTrue($reader->isDisposed());
		$this->assertFalse($stream->isDisposed());
	}

	public static function provider_isEnd()
	{
		return [
			[[1, 1, 1], "abc", Encoding::getAscii()],
			[[1, 1, 1], "abc", Encoding::getUtf8()],
			[[2, 2, 2], "abc", Encoding::getUtf16()],
			[[4, 4, 4], "abc", Encoding::getUtf32()],
		];
	}

	#[DataProvider('provider_isEnd')]
	public function test_isEnd(array $readCounts, string $init, Encoding $encoding)
	{
		$stream = Stream::openTemporary();
		$stream->writeBinary($encoding->getBinary($init));
		$stream->seekHead();

		$reader = new StreamReader($stream, $encoding);

		foreach ($readCounts as $readCount) {
			$this->assertFalse($reader->isEnd());
			$actualRead = $stream->readBinary($readCount);
			$this->assertSame($readCount, $actualRead->count());
		}

		$actualLast = $stream->readBinary(1);
		$this->assertSame(0, $actualLast->count());
		$this->assertTrue($reader->isEnd());
	}

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
		$stream = Stream::openTemporary();
		$reader = new StreamReader($stream, $encoding);

		if ($init->count()) {
			$stream->writeBinary($init);
		}
		$stream->seek($start, Stream::WHENCE_HEAD);

		$actual = $reader->readBom();

		$this->assertSame($expected, $actual);
	}

	public static function provider_readStringContents()
	{
		return [
			["", "", 0, Encoding::getAscii()],
			["", "", 0, Encoding::getUtf8()],
			["", "", 0, Encoding::getUtf16()],
			["", "", 0, Encoding::getUtf32()],

			["abc", "abc", 0, Encoding::getAscii()],
			["abc", "abc", 0, Encoding::getUtf8()],
			["abc", "abc", 0, Encoding::getUtf16()],
			["abc", "abc", 0, Encoding::getUtf32()],

			["🐎", "🐎", 0, Encoding::getUtf8()],
			["🐎", "🐎", 0, Encoding::getUtf16()],
			["🐎", "🐎", 0, Encoding::getUtf32()],

			["いう", "あいう", 3, Encoding::getUtf8()],
			["いう", "あいう", 2, Encoding::getUtf16()],
			["いう", "あいう", 4, Encoding::getUtf32()],
		];
	}

	#[DataProvider('provider_readStringContents')]
	public function test_readStringContents(string $expected, string $init, int $start, Encoding $encoding)
	{
		$stream = Stream::openTemporary();
		$reader = new StreamReader($stream, $encoding);

		$data = $encoding->getBinary($init);
		$stream->writeBinary($data);

		$stream->seek($start, Stream::WHENCE_HEAD);

		$actual = $reader->readStringContents();
		$this->assertSame($expected, $actual);
	}

	public static function provider_readLine()
	{
		return [
			[1, Encoding::getUtf8()],
			[1, Encoding::getUtf16()],
			[1, Encoding::getUtf32()],
			[2, Encoding::getUtf8()],
			[2, Encoding::getUtf16()],
			[2, Encoding::getUtf32()],
			[3, Encoding::getUtf8()],
			[3, Encoding::getUtf16()],
			[3, Encoding::getUtf32()],
			[4, Encoding::getUtf8()],
			[4, Encoding::getUtf16()],
			[4, Encoding::getUtf32()],
			[5, Encoding::getUtf8()],
			[5, Encoding::getUtf16()],
			[5, Encoding::getUtf32()],
			[6, Encoding::getUtf8()],
			[6, Encoding::getUtf16()],
			[6, Encoding::getUtf32()],
			[7, Encoding::getUtf8()],
			[7, Encoding::getUtf16()],
			[7, Encoding::getUtf32()],
			[8, Encoding::getUtf8()],
			[8, Encoding::getUtf16()],
			[8, Encoding::getUtf32()],
			[512, Encoding::getUtf8()],
			[512, Encoding::getUtf16()],
			[512, Encoding::getUtf32()],
			[1024, Encoding::getUtf8()],
			[1024, Encoding::getUtf16()],
			[1024, Encoding::getUtf32()],
		];
	}

	#[DataProvider('provider_readLine')]
	public function test_readLine_lastNoNewline($bufferSize, Encoding $encoding)
	{
		$stream = Stream::openTemporary();

		$stream->writeBinary($encoding->getBinary("ABC\r\n"));
		$stream->writeBinary($encoding->getBinary("DEF\r"));
		$stream->writeBinary($encoding->getBinary("GHI"));

		$stream->seekHead();

		$reader = new StreamReader($stream, $encoding);
		$reader->bufferSize = $bufferSize;

		$actual = [];

		while (!$reader->isEnd()) {
			$line = $reader->readLine();
			$actual[] = $line;
		}

		$this->assertSame(['ABC', 'DEF', 'GHI'], $actual, $encoding->name);
	}

	#[DataProvider('provider_readLine')]
	public function test_readLine_lastNewline($bufferSize, Encoding $encoding)
	{
		$stream = Stream::openTemporary();

		$stream->writeBinary($encoding->getBinary("ABC\r\n"));
		$stream->writeBinary($encoding->getBinary("DEF\r"));
		$stream->writeBinary($encoding->getBinary("GHI\n"));

		$stream->seekHead();

		$reader = new StreamReader($stream, $encoding);
		$reader->bufferSize = $bufferSize;

		$actual = [];

		while (!$reader->isEnd()) {
			$line = $reader->readLine();
			$actual[] = $line;
		}

		$this->assertSame(['ABC', 'DEF', 'GHI', ''], $actual, $encoding->name);
	}

	public static function provider_readLine_empty()
	{
		return [
			[Encoding::getUtf8()],
		];
	}

	#[DataProvider('provider_readLine_empty')]
	public function test_readLine_empty(Encoding $encoding)
	{
		$stream = Stream::openTemporary();
		$reader = new StreamReader($stream, $encoding);
		$actual = $reader->readLine();
		$this->assertEmpty("", $actual);
	}

	#endregion
}
