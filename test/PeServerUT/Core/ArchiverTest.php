<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\ArchiveEntry;
use PeServer\Core\Archiver;
use PeServer\Core\Binary;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\Path;
use PeServer\Core\Throws\ArchiveException;
use PeServerTest\TestClass;
use ZipArchive;

class ArchiverTest extends TestClass
{
	public function test_gzip()
	{
		$a = new Binary('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
		$b = Archiver::compressGzip($a);
		$c = Archiver::extractGzip($b);
		$this->assertSame($a->raw, $c->raw);
	}

	public function test_compressGzip_throw()
	{
		$a = new Binary('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
		$this->expectException(ArchiveException::class);
		Archiver::compressGzip($a, 10);
		$this->fail();
	}

	public function test_extractGzip_throw()
	{
		$a = new Binary('');
		$this->expectException(ArchiveException::class);
		Archiver::extractGzip($a);
		$this->fail();
	}

	public function test_compressZip_throw_empty_exists()
	{
		$dir = $this->testDir();
		$path = $dir->createFile("a.zip");
		$this->expectException(ArchiveException::class);
		$this->expectExceptionMessage("code: " . (string)ZipArchive::ER_EXISTS);
		Archiver::compressZip($path, []);
		$this->fail();
	}

	public function test_compressZip_throw_dummy_exists()
	{
		$dir = $this->testDir();
		$path = $dir->createFile("a.zip", new Binary("PK\x05\x06\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00")); // 7zipで空のZIP作ったデータ
		$this->expectException(ArchiveException::class);
		$this->expectExceptionMessage("code: " . (string)ZipArchive::ER_EXISTS);
		Archiver::compressZip($path, []);
		$this->fail();
	}

	public function test_compressZip_throw_empty_entryFiles()
	{
		$dir = $this->testDir();
		$path = $dir->newPath("a.zip");
		$this->expectException(ArchiveException::class);
		$this->expectExceptionMessage("empty: \$entryFiles");
		Archiver::compressZip($path, []);
		$this->fail();
	}

	public function test_compressZip()
	{
		$dir = $this->testDir();
		$zipPath = $dir->newPath("a.zip");
		$entryFile1 = new ArchiveEntry(
			$dir->createFile("a.txt", new Binary("ABC")),
			"A.txt"
		);
		$entryFile2 = new ArchiveEntry(
			$dir->createFile("b.txt", new Binary("DEF")),
			"dir/b.txt"
		);
		$entryFile3 = new ArchiveEntry(
			$dir->createFile("c.txt", new Binary("DEF")),
			"win\\c.txt"
		);

		Archiver::compressZip($zipPath, [
			$entryFile1,
			$entryFile2,
			$entryFile3,
		]);

		$actual = new ZipArchive();
		$actual->open($zipPath, ZipArchive::RDONLY);
		$this->assertSame(3, $actual->numFiles);
		$extractDirPath = $dir->createDirectory("extract");
		$actual->extractTo($extractDirPath);

		$files = Directory::getFiles($extractDirPath, true);
		$this->assertContains(Path::combine($extractDirPath, "A.txt"), $files);
		$this->assertContains(Path::combine($extractDirPath, "dir", "b.txt"), $files);
		$this->assertContains(Path::combine($extractDirPath, "win", "c.txt"), $files);
	}
}
