<?php

declare(strict_types=1);

namespace PeServerUT\Core\Archive;

use PeServer\Core\Archive\ArchiveEntry;
use PeServer\Core\Throws\ArgumentException;
use PeServerTest\TestClass;
use PHPUnit\Framework\Attributes\TestWith;

class ArchiveEntryTest extends TestClass
{
	#[TestWith([""])]
	#[TestWith([" "])]
	public function test_constructor_throw_path($input)
	{
		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage("path");

		new ArchiveEntry($input, "a");
		$this->fail();
	}

	#[TestWith([""])]
	#[TestWith([" "])]
	public function test_constructor_throw_entry($input)
	{
		$this->expectException(ArgumentException::class);
		$this->expectExceptionMessage("entry");

		new ArchiveEntry("a", $input);
		$this->fail();
	}
}
