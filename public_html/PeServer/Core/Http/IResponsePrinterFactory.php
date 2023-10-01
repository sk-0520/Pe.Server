<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

interface IResponsePrinterFactory
{
	public function createResponsePrinter(HttpRequest $request, HttpResponse $response): ResponsePrinter;
}
