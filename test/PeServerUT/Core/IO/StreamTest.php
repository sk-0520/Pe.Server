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
			$actual = $this->callConstructor(Stream::class, [$resource]);
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


	public function test_createTemporaryFile()
	{
		try {
			$stream = Stream::createTemporaryFile();
			$stream->dispose();
			$this->success();
		} catch (Throwable $ex) {
			$this->fail($ex->__toString());
		}
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

	public function test_readBinary()
	{
		$testDir = $this->testDir();
		$path = $testDir->createFile(__FUNCTION__);
		$stream = Stream::open($path, Stream::MODE_EDIT);
		$stream->writeBinary(new Binary('ABCDEFG'));
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
}
