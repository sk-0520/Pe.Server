<?php

declare(strict_types=1);

namespace PeServerTest\Core;

use PeServer\Core\Archiver;
use PeServer\Core\Binary;
use PeServerTest\TestClass;

class ArchiverTest extends TestClass
{
	public function test_gzip()
	{
		$a = new Binary('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
		$b = Archiver::compressGzip($a);
		$c = Archiver::extractGzip($b);
		$this->assertEquals($a->getRaw(), $c->getRaw());
	}
}
