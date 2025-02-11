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

	public function test_read()
	{
		$dir = $this->testDir();
		$path = $dir->createFile("a.txt", new Binary("\x01\x02\x03\x04\x05\x06\x07\x08"));

		$stream = FileCleanupStream::read($path);
		$this->assertSame("\x01\x02\x03\x04", $stream->readBinary(4)->raw);
		$this->assertSame("\x05\x06\x07\x08", $stream->readBinaryContents()->raw);
		$stream->dispose();
		$this->assertFalse(File::exists($path));
	}
	#endregion
}
