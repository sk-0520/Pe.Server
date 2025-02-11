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
use PeServer\Core\Throws\StreamException;
use PeServerTest\TestClass;

class FileCleanupStreamTest extends TestClass
{
	#region function

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

	public function test_writeBom_throw()
	{
		$dir = $this->testDir();
		$path = $dir->createFile("a.txt");

		$stream = FileCleanupStream::read($path);

		$this->expectException(StreamException::class);
		$stream->writeBom();
		$this->fail();
	}

	public function test_writeString_throw()
	{
		$dir = $this->testDir();
		$path = $dir->createFile("a.txt");

		$stream = FileCleanupStream::read($path);

		$this->expectException(StreamException::class);
		$stream->writeString("abc");
		$this->fail();
	}

	public function test_writeLine_throw()
	{
		$dir = $this->testDir();
		$path = $dir->createFile("a.txt");

		$stream = FileCleanupStream::read($path);

		$this->expectException(StreamException::class);
		$stream->writeLine("abc");
		$this->fail();
	}


	#endregion
}
