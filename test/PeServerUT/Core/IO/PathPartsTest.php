<?php

declare(strict_types=1);

namespace PeServerUT\Core\IO;

use PeServer\Core\IO\PathParts;
use PeServerTest\TestClass;

class PathPartsTest extends TestClass
{
	public function test_toString()
	{
		$pp = new PathParts('d', 'f.txt', 'f', 'txt');
		$this->assertSame('d' . DIRECTORY_SEPARATOR . 'f.txt', $pp->toString());
	}

	public function test___toString()
	{
		$pp = new PathParts('d', 'f.txt', 'f', 'txt');
		$this->assertSame('d' . DIRECTORY_SEPARATOR . 'f.txt', (string)$pp);
	}
}
