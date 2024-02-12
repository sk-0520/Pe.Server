<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

/**
 * ResponsePrinter生成器。
 *
 * @see \PeServer\Core\Http\ResponsePrinter
 *
 */
interface IResponsePrinterFactory
{
	public function createResponsePrinter(HttpRequest $request, HttpResponse $response): ResponsePrinter;
}
