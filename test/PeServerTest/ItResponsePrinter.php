<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\ResponsePrinter;

class ItResponsePrinter extends ResponsePrinter
{
	private static HttpResponse|null $test_response = null;

	public function __construct(HttpRequest $request, HttpResponse $response)
	{
		self::$test_response = null;
		parent::__construct($request, $response);
	}

	public function execute(): void
	{
		self::$test_response = $this->response;
	}

	public static function getResponse(): HttpResponse|null
	{
		return self::$test_response;
	}
}
