<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\DiFactoryTrait;
use PeServer\Core\DI\IDiContainer;

class ResponsePrinterFactory extends DiFactoryBase implements IResponsePrinterFactory
{
	use DiFactoryTrait;

	#region IResponsePrinterFactory

	public function createResponsePrinter(HttpRequest $request, HttpResponse $response): ResponsePrinter
	{
		/** @var ResponsePrinter */
		return $this->container->new(ResponsePrinter::class, [$request, $response]);
	}

	#endregion
}
