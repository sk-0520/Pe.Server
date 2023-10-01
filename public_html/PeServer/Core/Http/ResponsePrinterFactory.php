<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

use PeServer\Core\DI\DiFactoryBase;
use PeServer\Core\DI\IDiContainer;

class ResponsePrinterFactory extends DiFactoryBase implements IResponsePrinterFactory
{
	public function __construct(IDiContainer $container)
	{
		parent::__construct($container);
	}

	#region IResponsePrinterFactory

	public function createResponsePrinter(HttpRequest $request, HttpResponse $response): ResponsePrinter
	{
		return $this->container->new(ResponsePrinter::class, [$request, $response]);
	}

	#endregion
}
