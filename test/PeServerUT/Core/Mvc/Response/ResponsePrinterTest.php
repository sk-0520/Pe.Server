<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc\Response;

use PeServer\Core\Binary;
use PeServer\Core\Http\Client\StringContent;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\ICallbackContent;
use PeServer\Core\IO\Stream;
use PeServer\Core\IO\StreamMetaData;
use PeServer\Core\Mvc\Response\ResponsePrinter;
use PeServer\Core\OutputBuffer;
use PeServer\Core\Text;
use PeServerTest\TestClass;
use PeServerTest\TestSetupSpecialStore;
use PHPUnit\Framework\Attributes\DataProvider;

class ResponsePrinterTest extends TestClass
{
	private function createRequest(): HttpRequest
	{
		return new HttpRequest(
			new TestSetupSpecialStore(),
			HttpMethod::Get,
			HttpHeader::createClientRequestHeader(),
			[]
		);
	}

	public static function provider_getContentLength()
	{
		$seekableStream = Stream::openMemory();
		$seekableStream->writeBinary(new Binary("123"));
		$seekableStream->seekHead();

		return [
			[
				ICallbackContent::UNKNOWN,
				null,
			],
			[
				3,
				'abc',
			],
			[
				(new Binary('あいう'))->count(),
				'あいう', // utf8 前提テスト
			],
			[
				2,
				new Binary("\x00\xff"),
			],
			[
				3,
				$seekableStream,
			],
			[
				3,
				new class implements ICallbackContent
				{
					public function output(): void
					{
						echo 'a';
						echo 'b';
						echo 'c';
					}
					public function getLength(): int
					{
						return 3;
					}
				},
			],
		];
	}

	#[DataProvider('provider_getContentLength')]
	public function test_getContentLength($expected, $input)
	{
		$req = $this->createRequest();
		$res = new HttpResponse();
		$res->content = $input;

		$rp = new ResponsePrinter($req, $res);
		$actual = $this->callInstanceMethod($rp, 'getContentLength');
		$this->assertSame($expected, $actual);
	}

	public static function provider_output()
	{
		$seekableStream = Stream::openMemory();
		$seekableStream->writeBinary(new Binary("123"));
		$seekableStream->seekHead();

		return [
			[
				new Binary(''),
				null,
			],
			[
				new Binary('abc'),
				'abc',
			],
			[
				new Binary('あいう'),
				'あいう', // utf8 前提テスト
			],
			[
				new Binary("\x00\xff"),
				new Binary("\x00\xff"),
			],
			[
				new Binary("123"),
				$seekableStream,
			],
			[
				new Binary("abc"),
				new class implements ICallbackContent
				{
					public function output(): void
					{
						echo 'a';
						echo 'b';
						echo 'c';
					}
					public function getLength(): int
					{
						return 3;
					}
				},
			],
		];
	}

	#[DataProvider('provider_output')]
	public function test_output(Binary $expected, $input)
	{
		$req = $this->createRequest();
		$res = new HttpResponse();
		$res->content = $input;

		$rp = new ResponsePrinter($req, $res);
		$actual = OutputBuffer::get(fn() => $rp->execute());
		$this->assertSame($expected->raw, $actual->raw);
	}

	public function test_output_unseeable()
	{
		$stream = LocalUnseekableStream::init(new Binary("abc"));

		$req = $this->createRequest();
		$res = new HttpResponse();
		$res->content = $stream;

		$rp = new ResponsePrinter($req, $res);
		$actual = OutputBuffer::get(fn() => $rp->execute());
		$this->assertSame("3\r\nabc\r\n0\r\n\r\n", $actual->raw);
	}
}


final class LocalUnseekableStream extends Stream
{
	public static function init(?Binary $input = null): self
	{
		$stream = self::openMemory();

		if ($input !== null) {
			$stream->writeBinary($input);
			$stream->seekHead();
		}

		return $stream;
	}

	#region Stream

	public function getMetaData(): StreamMetaData
	{
		$values = stream_get_meta_data($this->resource);
		$values["seekable"] = false;
		return StreamMetaData::createFromStream($values);
	}

	#endregion
}
