<?php

declare(strict_types=1);

namespace PeServerUT\Core\IO;

use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use stdClass;
use TypeError;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;
use PeServer\Core\IO\Stream;
use PeServer\Core\ResourceBase;
use PeServer\Core\SizeConverter;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\ResourceInvalidException;
use PeServer\Core\Throws\StreamException;
use PeServerTest\Data;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use Throwable;

class StreamTest extends TestClass
{
	private static function delete(string $path)
	{
		try {
			if (File::exists($path)) {
				File::removeFile($path);
				IOUtility::clearCache($path);
			}
		} catch (Throwable) {
			//NOP
		}
	}

	public function test_constructor()
	{
		$path = File::createTemporaryFilePath();
		try {
			$resource = fopen($path, 'w');
			$actual = new Stream($resource);
			$actual->dispose();
			$this->assertTrue((bool)$resource);
			$this->assertSame('Unknown', get_resource_type($resource));
		} finally {
			self::delete($path);
		}
	}

	/*
	リソース型がねーんだわ
	public function test_constructor_invalid_throw()
	{
		$this->expectException(ResourceInvalidException::class);
		$f = File::createTemporaryFilePath();
		$resource = gzopen($f, 'w');
		try {
			new Stream($resource);
		} finally {
			gzclose($resource);
		}
		$this->fail();
	}
	*/

	public function test_create()
	{
		$path = Path::combine(Directory::getTemporaryDirectory(), __FUNCTION__ . '.txt');

		self::delete($path);

		$stream = Stream::create($path);
		$this->assertTrue(File::exists($path));
		$stream->dispose();

		IOUtility::clearCache($path);

		$this->expectException(IOException::class);
		try {
			Stream::create($path);
		} finally {
			self::delete($path);
		}
		$this->fail();
	}

	public function test_open()
	{
		$path = Path::combine(Directory::getTemporaryDirectory(), __FUNCTION__ . '.txt');

		self::delete($path);

		try {
			Stream::open($path, Stream::MODE_READ);
			$this->fail();
		} catch (IOException) {
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		try {
			Stream::open($path, Stream::MODE_WRITE);
			$this->fail();
		} catch (IOException) {
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		try {
			Stream::open($path, Stream::MODE_EDIT);
			$this->fail();
		} catch (IOException) {
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		File::writeContent($path, new Binary(__FILE__ . ':' . __FUNCTION__ . ':' . __LINE__));
		IOUtility::clearCache($path);

		try {
			$stream = Stream::open($path, Stream::MODE_READ);
			$stream->dispose();
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		try {
			$stream = Stream::open($path, Stream::MODE_WRITE);
			$stream->dispose();
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		try {
			$stream = Stream::open($path, Stream::MODE_EDIT);
			$stream->dispose();
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}
	}

	public function test_openOrCreate()
	{
		$path = Path::combine(Directory::getTemporaryDirectory(), __FUNCTION__ . '.txt');

		self::delete($path);

		try {
			$stream = Stream::openOrCreate($path, Stream::MODE_READ);
			$stream->dispose();
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		try {
			$stream = Stream::openOrCreate($path, Stream::MODE_WRITE);
			$stream->dispose();
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		try {
			$stream = Stream::openOrCreate($path, Stream::MODE_EDIT);
			$stream->dispose();
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		File::writeContent($path, new Binary(__FILE__ . ':' . __FUNCTION__ . ':' . __LINE__));
		IOUtility::clearCache($path);

		try {
			$stream = Stream::openOrCreate($path, Stream::MODE_READ);
			$stream->dispose();
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		try {
			$stream = Stream::openOrCreate($path, Stream::MODE_WRITE);
			$stream->dispose();
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}

		try {
			$stream = Stream::openOrCreate($path, Stream::MODE_EDIT);
			$stream->dispose();
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->getMessage());
		}
	}

	public static function provider_openTemporary()
	{
		return [
			[0],
			[1],
			[1000],
			[1024 * 4],
		];
	}

	#[DataProvider('provider_openTemporary')]
	public function test_openTemporary(int $memoryByteSize)
	{
		Stream::openTemporary($memoryByteSize);
		$this->success();
	}


	public function test_openTemporary_throw()
	{
		$this->expectException(ArgumentException::class);
		Stream::openTemporary(-1);
	}

	public function test_getState()
	{
		$stream = Stream::open(__FILE__, Stream::MODE_READ);
		$state = $stream->getState();
		$this->success();
	}

	public function test_getMetaData()
	{
		$stream = Stream::open(__FILE__, Stream::MODE_READ);
		$state = $stream->getMetaData();
		$this->success();
	}

	public function test_getMetaData_temp()
	{
		$stream = Stream::openTemporary(10);
		$memory = $stream->getMetaData();
		$this->assertSame('php://temp/maxmemory:10', $memory->uri);

		//こっからファイル名とれる思てんけどなぁ
		// $stream->writeBinary(new Binary('0123456789+'));
		// $stream->flush();
		// $file = $stream->getMetaData();
		// $this->assertSame('php://temp/maxmemory:10', $file->uri);
	}


	public function test_write_read_binary()
	{
		$expected = new Binary("ABC");

		$stream = Stream::openMemory(); // 一応ここだけ php://memory を試しておく

		$writeLength = $stream->writeBinary($expected);
		$this->assertSame($expected->count(), $writeLength);

		$stream->seekHead();

		$actual = $stream->readBinary($expected->count());
		$this->assertSame($expected->raw, $actual->raw);
	}

	public function test_write_writeBinary_throw()
	{
		$testDir = $this->testDir();
		$path = $testDir->createFile(__FUNCTION__);
		$stream = Stream::open($path, Stream::MODE_READ);

		$this->expectException(StreamException::class);
		$stream->writeBinary(new Binary('error'));
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
	public function test_writeBom(int $expected, Binary $data, Encoding $encoding)
	{
		$stream = Stream::openTemporary(encoding: $encoding);

		if ($data->count()) {
			$stream->writeBinary($data);
		}
		$actual = $stream->writeBom();

		$this->assertSame($expected, $actual);
	}

	public function test_write_read_string()
	{
		$expected = "ABC";

		$stream = Stream::openTemporary();

		$writeLength = $stream->writeString($expected);
		$this->assertSame(Text::getByteCount($expected), $writeLength);

		$stream->seekHead();

		$actual = $stream->readStringContents(Text::getByteCount($expected));
		$this->assertSame($expected, $actual);
	}

	public function test_readBinary()
	{
		$testDir = $this->testDir();
		$path = $testDir->createFile(__FUNCTION__);
		$stream = Stream::open($path, Stream::MODE_EDIT);
		$stream->writeString('ABCDEFG');
		$stream->seekHead();

		$actual = $stream->readBinary(3);
		$this->assertSame('ABC', $actual->raw);
	}

	public function test_readBinary_throw()
	{
		$testDir = $this->testDir();
		$path = $testDir->createFile(__FUNCTION__);
		$stream = Stream::open($path, Stream::MODE_WRITE);

		$this->expectException(StreamException::class);
		$stream->readBinary(100);
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
	public function test_readBom(bool $expected, Binary $data, int $start, Encoding $encoding)
	{
		$stream = Stream::openTemporary(encoding: $encoding);

		if ($data->count()) {
			$stream->writeBinary($data);
		}
		$stream->seek($start, Stream::WHENCE_SET);

		$actual = $stream->readBom();

		$this->assertSame($expected, $actual);
	}

	public static function provider_readLine_bufferSize()
	{
		return [
			[1],
			[2],
			[3],
			[4],
			[5],
			[6],
			[7],
			[8],
			[512],
			[1024],
		];
	}

	#[DataProvider('provider_readLine_bufferSize')]
	public function test_readLine_lastNoNewline($bufferSize)
	{
		$stream = Stream::openTemporary();

		$stream->newLine = "\r\n";
		$stream->writeLine('ABC');
		$stream->newLine = "\r";
		$stream->writeLine('DEF');
		$stream->newLine = "\n";
		$stream->writeString('GHI');

		$stream->seekHead();

		$actual = [];

		while (!$stream->isEnd()) {
			$line = $stream->readLine($bufferSize);
			$actual[] = $line;
		}

		$this->assertSame(['ABC', 'DEF', 'GHI'], $actual);
	}

	#[DataProvider('provider_readLine_bufferSize')]
	public function test_readLine_lastNewline($bufferSize)
	{
		$stream = Stream::openTemporary();

		$stream->newLine = "\r\n";
		$stream->writeLine('ABC');
		$stream->newLine = "\r";
		$stream->writeLine('DEF');
		$stream->newLine = "\n";
		$stream->writeLine('GHI');

		$stream->seekHead();

		$actual = [];

		while (!$stream->isEnd()) {
			$line = $stream->readLine($bufferSize);
			$actual[] = $line;
		}

		$this->assertSame(['ABC', 'DEF', 'GHI', ''], $actual);
	}

	public function test_readLine_throw()
	{
		$stream = Stream::openTemporary();
		$this->expectException(ArgumentException::class);
		$stream->readLine(0);
		$this->fail();
	}
}
