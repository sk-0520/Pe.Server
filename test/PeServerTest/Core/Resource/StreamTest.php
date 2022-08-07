<?php

declare(strict_types=1);

namespace PeServerTest\Core\Resource;

use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use \stdClass;
use \TypeError;
use PeServer\Core\IOUtility;
use PeServer\Core\PathUtility;
use PeServer\Core\Resource\Stream;
use PeServer\Core\Resource\ResourceBase;
use PeServer\Core\SizeConverter;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\ResourceInvalidException;
use PeServerTest\Data;
use PeServerTest\TestClass;
use Throwable;

class StreamTest extends TestClass
{
	private static function delete(string $path)
	{
		try {
			if (IOUtility::existsFile($path)) {
				IOUtility::removeFile($path);
				IOUtility::clearCache($path);
			}
		} catch (Throwable) {
			//NONE
		}
	}

	public function test_constructor()
	{
		$path = IOUtility::createTemporaryFilePath();
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
		$f = IOUtility::createTemporaryFilePath();
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
		$path = PathUtility::combine(IOUtility::getTemporaryDirectory(), __FUNCTION__ . '.txt');

		self::delete($path);

		$stream = Stream::create($path);
		$this->assertTrue(IOUtility::existsFile($path));
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
		$path = PathUtility::combine(IOUtility::getTemporaryDirectory(), __FUNCTION__ . '.txt');

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

		IOUtility::writeContent($path, __FILE__ . ':' . __FUNCTION__ . ':' . __LINE__);
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
		$path = PathUtility::combine(IOUtility::getTemporaryDirectory(), __FUNCTION__ . '.txt');

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

		IOUtility::writeContent($path, __FILE__ . ':' . __FUNCTION__ . ':' . __LINE__);
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

	public function test_write_read_binary()
	{
		$expected = new Binary("ABC");

		$stream = Stream::openMemory(); // 一応ここだけ php://memory を試しておく

		$writeLength = $stream->writeBinary($expected);
		$this->assertSame($expected->getLength(), $writeLength);

		$stream->seekHead();

		$actual = $stream->readBinary($expected->getLength());
		$this->assertSame($expected->getRaw(), $actual->getRaw());
	}

	public function test_write_read_string()
	{
		$expected = "ABC";

		$stream = Stream::openTemporary();

		$writeLength = $stream->writeString($expected);
		$this->assertSame(StringUtility::getByteCount($expected), $writeLength);

		$stream->seekHead();

		$actual = $stream->readStringContents(StringUtility::getByteCount($expected));
		$this->assertSame($expected, $actual);
	}

	public function provider_readLine_bufferSize()
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

	/** @dataProvider provider_readLine_bufferSize */
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

		while (!$stream->eof()) {
			$line = $stream->readLine($bufferSize);
			$actual[] = $line;
		}

		$this->assertSame(['ABC', 'DEF', 'GHI'], $actual);
	}

	/** @dataProvider provider_readLine_bufferSize */
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

		while (!$stream->eof()) {
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
