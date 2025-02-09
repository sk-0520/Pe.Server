<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc;

use Iterator;
use PeServer\Core\Binary;
use PeServer\Core\Http\ICallbackContent;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\ChunkedContentBase;
use PeServer\Core\OutputBuffer;
use PeServerTest\TestClass;

class ChunkedContentBaseTest extends TestClass
{
	#region function

	public function test_output()
	{
		$obj = new class extends ChunkedContentBase
		{
			public function __construct()
			{
				parent::__construct(Mime::TEXT);
			}

			#region ChunkedContentBase

			protected function getIterator(): Iterator
			{
				yield new Binary("abc");
				yield new Binary("defghi");
				yield new Binary("jklmnoopq");
			}

			#endregion
		};

		$this->assertSame(ICallbackContent::UNKNOWN, $obj->getLength());
		$this->assertSame(Mime::TEXT, $obj->mime);
		$actual = OutputBuffer::get(fn() => $obj->output());
		$this->assertSame("3\r\nabc\r\n6\r\ndefghi\r\n9\r\njklmnoopq\r\n0\r\n\r\n", $actual->raw);
	}

	#endregion
}
