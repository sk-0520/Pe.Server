<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Content;

use Iterator;
use PeServer\Core\Binary;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\ChunkedContentBase;
use PeServer\Core\Mvc\Content\CallbackChunkedContent;
use PeServer\Core\OutputBuffer;
use PeServerTest\TestClass;

class CallbackChunkedContentTest extends TestClass
{
	#region function

	public function test_output()
	{
		$obj = new CallbackChunkedContent(function () {
			yield new Binary("abc");
			yield new Binary("defghi");
			yield new Binary("jklmnoopq");
			yield new Binary("rstuvwxyz012");
		}, Mime::STREAM);

		$this->assertSame(Mime::STREAM, $obj->mime);
		$actual = OutputBuffer::get(fn() => $obj->output());
		$this->assertSame("3\r\nabc\r\n6\r\ndefghi\r\n9\r\njklmnoopq\r\nc\r\nrstuvwxyz012\r\n0\r\n\r\n", $actual->raw);
	}

	#endregion
}
