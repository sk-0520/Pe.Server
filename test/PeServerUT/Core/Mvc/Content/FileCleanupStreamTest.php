<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Content;

use Iterator;
use PeServer\Core\Binary;
use PeServer\Core\Http\ICallbackContent;
use PeServer\Core\IO\File;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\ChunkedContentBase;
use PeServer\Core\Mvc\Content\FileCleanupStream;
use PeServer\Core\OutputBuffer;
use PeServer\Core\Throws\IOException;
use PeServer\Core\Throws\NotSupportedException;
use PeServer\Core\Throws\StreamException;
use PeServerTest\TestClass;

class FileCleanupStreamTest extends TestClass
{
	#region function

	public function test_new_throw()
	{
		$this->expectException(NotSupportedException::class);
		FileCleanupStream::new("", "");
	}

	public function test_create_throw()
	{
		$this->expectException(NotSupportedException::class);
		FileCleanupStream::create("");
	}

	public function test_open_throw()
	{
		$this->expectException(NotSupportedException::class);
		FileCleanupStream::open("", FileCleanupStream::MODE_READ);
	}

	public function test_openOrCreate_throw()
	{
		$this->expectException(NotSupportedException::class);
		FileCleanupStream::openOrCreate("", FileCleanupStream::MODE_READ);
	}

	public function test_openStandardInput_throw()
	{
		$this->expectException(NotSupportedException::class);
		FileCleanupStream::openStandardInput();
	}

	public function test_openStandardOutput_throw()
	{
		$this->expectException(NotSupportedException::class);
		FileCleanupStream::openStandardOutput();
	}

	public function test_openStandardError_throw()
	{
		$this->expectException(NotSupportedException::class);
		FileCleanupStream::openStandardError();
	}

	public function test_openMemory_throw()
	{
		$this->expectException(NotSupportedException::class);
		FileCleanupStream::openMemory();
	}

	public function test_openTemporary_throw()
	{
		$this->expectException(NotSupportedException::class);
		FileCleanupStream::openTemporary();
	}

	public function test_createTemporaryFile_throw()
	{
		$this->expectException(NotSupportedException::class);
		$stream = FileCleanupStream::createTemporaryFile();
	}


	public function test_read_throw()
	{
		$dir = $this->testDir();
		$path = $dir->newPath("a.txt");

		$this->expectException(IOException::class);
		FileCleanupStream::read($path);
		$this->fail();
	}

	public function test_read_all_read()
	{
		$dir = $this->testDir();
		$path = $dir->createFile("a.txt", new Binary("\x01\x02\x03\x04\x05\x06\x07\x08"));

		$stream = FileCleanupStream::read($path);
		$this->assertSame("\x01\x02\x03\x04", $stream->readBinary(4)->raw);
		$this->assertSame("\x05\x06\x07\x08", $stream->readBinaryContents()->raw);
		$stream->dispose();
		$this->assertFalse(File::exists($path));
	}

	public function test_read_1_read()
	{
		$dir = $this->testDir();
		$path = $dir->createFile("a.txt", new Binary("\x01\x02\x03\x04\x05\x06\x07\x08"));

		$stream = FileCleanupStream::read($path);
		$this->assertSame("\x01\x02\x03\x04", $stream->readBinary(4)->raw);
		$stream->dispose();
		$this->assertTrue(File::exists($path));
	}

	public function test_read_0_size_0_read()
	{
		$dir = $this->testDir();
		$path = $dir->createFile("a.txt");

		$stream = FileCleanupStream::read($path);
		$stream->dispose();
		$this->assertTrue(File::exists($path));
	}

	public function test_read_0_size_1_read()
	{
		$dir = $this->testDir();
		$path = $dir->createFile("a.txt");

		$stream = FileCleanupStream::read($path);
		$this->assertCount(0, $stream->readBinaryContents());
		$stream->dispose();
		$this->assertFalse(File::exists($path));
	}


	public function test_writeBinary_throw()
	{
		$dir = $this->testDir();
		$path = $dir->createFile("a.txt");

		$stream = FileCleanupStream::read($path);

		$this->expectException(StreamException::class);
		$stream->writeBinary(new Binary("abc"));
		$this->fail();
	}

	#endregion
}
