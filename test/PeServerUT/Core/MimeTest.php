<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\Binary;
use PeServer\Core\Throws\ArgumentException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PeServer\Core\Mime;
use PeServer\Core\Throws\FileNotFoundException;

class MimeTest extends TestClass
{
	public static function provider_fromFileName()
	{
		return [
			["text/plain", "a.txt"],
		];
	}

	#[DataProvider('provider_fromFileName')]
	public function test_fromFileName(string $expected, string $fileName)
	{
		$testDir = $this->testDir();
		$path = $testDir->createFile($fileName, new Binary('file'));
		$actual = Mime::fromFileName($path);
		$this->assertSame($expected, $actual);
	}

	public static function provider_fromFileName_throw()
	{
		return [
			[ArgumentException::class, ""],
			[ArgumentException::class, " "],
			[FileNotFoundException::class, "/dev/null/NUL"],
		];
	}

	#[DataProvider('provider_fromFileName_throw')]
	public function test_fromFileName_throw(string $exception, string $fileName)
	{
		$this->expectException($exception);
		Mime::fromFileName($fileName);
		$this->fail();
	}
}
