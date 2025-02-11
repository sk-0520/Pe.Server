<?php

declare(strict_types=1);

namespace PeServerUT\Core\Mvc;

use PeServer\Core\Binary;
use PeServer\Core\Http\Client\StringContent;
use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\ICallbackContent;
use PeServer\Core\Mvc\ResponsePrinter;
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
		return [
			[
				ICallbackContent::UNKNOWN, null,
			],
			[
				new Binary('abc'), 'abc',
			],
			[
				new Binary('あいう'), 'あいう', // utf8 前提テスト
			],
			[
				new Binary("\x00\xff"), new Binary("\x00\xff"),
			],
			[
				new Binary("abc"), new class implements ICallbackContent
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
	public function test_getContentLength(Binary|int $expected, $input)
	{
		$req = $this->createRequest();
		$res = new HttpResponse();
		$res->content = $input;

		$rp = new ResponsePrinter($req, $res);
		$actual = $this->callInstanceMethod($rp, 'getContentLength');
		if ($expected instanceof Binary) {
			$this->assertSame($expected->count(), $actual);
		} else {
			$this->assertSame($expected, $actual);
		}
	}

	public static function provider_output()
	{
		return [
			[
				new Binary(''), null,
			],
			[
				new Binary('abc'), 'abc',
			],
			[
				new Binary('あいう'), 'あいう', // utf8 前提テスト
			],
			[
				new Binary("\x00\xff"), new Binary("\x00\xff"),
			],
			[
				new Binary("abc"), new class implements ICallbackContent
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
	public function test_output(Binary|null $expected, $input)
	{
		$req = $this->createRequest();
		$res = new HttpResponse();
		$res->content = $input;

		$rp = new ResponsePrinter($req, $res);
		$actual = OutputBuffer::get(fn () => $this->callInstanceMethod($rp, 'output'));
		if ($expected instanceof Binary) {
			$this->assertSame($expected->raw, $actual->raw);
		} else {
			$this->assertSame($expected, $actual->raw);
		}
	}
}
