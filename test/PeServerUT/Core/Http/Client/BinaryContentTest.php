<?php

declare(strict_types=1);

namespace PeServerUT\Core\Http\Client;

use PeServer\Core\Binary;
use PeServer\Core\Encoding;
use PeServer\Core\Http\Client\BinaryContent;
use PeServer\Core\Mime;
use PeServer\Core\Text;
use PeServerTest\Data;
use PeServerTest\TestClass;

class BinaryContentTest extends TestClass
{
	public function test_default()
	{
		$bc = new BinaryContent(new Binary("abc"));
		$this->assertSame("abc", $bc->toBody()->raw);
		$this->assertFalse($bc->toHeader()->existsContentType());
	}
}
