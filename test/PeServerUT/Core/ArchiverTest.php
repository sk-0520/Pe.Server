<?php

declare(strict_types=1);

namespace PeServerUT\Core;

use PeServer\Core\Archiver;
use PeServer\Core\Binary;
use PeServerUT\TestClass;

class ArchiverTest extends TestClass
{
	public function test_gzip()
	{
		$a = new Binary('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
		$b = Archiver::compressGzip($a);
		$c = Archiver::extractGzip($b);
		$this->assertSame($a->getRaw(), $c->getRaw());
	}
}
