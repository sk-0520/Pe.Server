<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Content;

use Closure;
use Iterator;
use PeServer\Core\Binary;
use PeServer\Core\Http\ICallbackContent;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\Content\ChunkedContentBase;
use PeServer\Core\Mvc\Content\CallbackChunkedContent;
use PeServer\Core\Mvc\Content\CallbackEventStreamContent;
use PeServer\Core\Mvc\Content\EventStreamMessage;
use PeServer\Core\Mvc\Content\IDownloadContent;
use PeServer\Core\OutputBuffer;
use PeServer\Core\Throws\NotSupportedException;
use PeServerTest\TestClass;

class CallbackEventStreamContentTest extends TestClass
{
	#region function

	public function test_output_default()
	{
		$obj = new CallbackEventStreamContent(function () {
			yield new EventStreamMessage("abc");
			yield new EventStreamMessage("defghi");
			yield new EventStreamMessage("jklmnoopq");
			yield new EventStreamMessage("rstuvwxyz012");
		});

		$this->assertSame(ICallbackContent::UNKNOWN, $obj->getLength());

		$this->assertSame(Mime::EVENT_STREAM, $obj->mime);
		$actual = OutputBuffer::get(fn() => $obj->output());
		$this->assertSame("data: abc\r\n\r\ndata: defghi\r\n\r\ndata: jklmnoopq\r\n\r\ndata: rstuvwxyz012\r\n\r\ndata: <DONE>\r\n\r\n", $actual->raw);
	}

	public function test_output_close()
	{
		//phpcs:ignore PSR12.Classes.AnonClassDeclaration.SpaceAfterKeyword
		$obj = new class(function () {
			yield new EventStreamMessage("abc");
			yield new EventStreamMessage("defghi");
			yield new EventStreamMessage("jklmnoopq");
			yield new EventStreamMessage("rstuvwxyz012");
		}) extends CallbackEventStreamContent {
			public function __construct(Closure $callback)
			{
				parent::__construct($callback);
			}

			protected function outputClose(): void
			{
				$this->outputContent("data", new Binary("closed"));
			}
		};

		$this->assertSame(Mime::EVENT_STREAM, $obj->mime);
		$actual = OutputBuffer::get(fn() => $obj->output());
		$this->assertSame("data: abc\r\n\r\ndata: defghi\r\n\r\ndata: jklmnoopq\r\n\r\ndata: rstuvwxyz012\r\n\r\ndata: closed\r\n\r\n", $actual->raw);
	}

	public function test_output_text_lines()
	{
		$obj = new CallbackEventStreamContent(function () {
			yield new EventStreamMessage("1\r\n2");
			yield new EventStreamMessage("1\r2\n3");
			yield new EventStreamMessage("1\r2\n3\r\n4");
		});

		$this->assertSame(Mime::EVENT_STREAM, $obj->mime);
		$actual = OutputBuffer::get(fn() => $obj->output());
		$this->assertSame("data: 1\r\ndata: 2\r\n\r\ndata: 1\r\ndata: 2\r\ndata: 3\r\n\r\ndata: 1\r\ndata: 2\r\ndata: 3\r\ndata: 4\r\n\r\ndata: <DONE>\r\n\r\n", $actual->raw);
	}

	public function test_output_message()
	{
		$obj = new CallbackEventStreamContent(function () {
			yield new EventStreamMessage("text", event: "EVENT", id: "ID", retr: 123);
		});

		$this->assertSame(Mime::EVENT_STREAM, $obj->mime);
		$actual = OutputBuffer::get(fn() => $obj->output());
		$this->assertSame("event: EVENT\r\nid: ID\r\nretr: 123\r\ndata: text\r\n\r\ndata: <DONE>\r\n\r\n", $actual->raw);
	}

	public function test_output_array()
	{
		$obj = new CallbackEventStreamContent(function () {
			yield new EventStreamMessage([
				"number" => 123,
				"string" => "abc",
				"array" => [1, 2, 3],
				"obj" => ["key" => "value"]
			]);
		});

		$this->assertSame(Mime::EVENT_STREAM, $obj->mime);
		$actual = OutputBuffer::get(fn() => $obj->output());
		$this->assertSame("data: {\"number\":123,\"string\":\"abc\",\"array\":[1,2,3],\"obj\":{\"key\":\"value\"}}\r\n\r\ndata: <DONE>\r\n\r\n", $actual->raw);
	}

	public function test_output_object()
	{
		$obj = new CallbackEventStreamContent(function () {
			$obj = new class {
				public int $number = 123;
				public string $string = "abc";
				public array $array = [1, 2, 3];
				public array $obj = ["key" => "value"];
			};
			yield new EventStreamMessage($obj);
		});

		$this->assertSame(Mime::EVENT_STREAM, $obj->mime);
		$actual = OutputBuffer::get(fn() => $obj->output());
		$this->assertSame("data: {\"number\":123,\"string\":\"abc\",\"array\":[1,2,3],\"obj\":{\"key\":\"value\"}}\r\n\r\ndata: <DONE>\r\n\r\n", $actual->raw);
	}
	#endregion
}
