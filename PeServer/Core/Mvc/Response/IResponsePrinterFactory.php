<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Response;

use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;

/**
 * ResponsePrinter生成器。
 *
 * @see \PeServer\Core\Mvc\Response\ResponsePrinter
 *
 */
interface IResponsePrinterFactory
{
	public function createResponsePrinter(HttpRequest $request, HttpResponse $response): ResponsePrinter;
}
