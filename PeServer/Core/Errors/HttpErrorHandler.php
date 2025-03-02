<?php

declare(strict_types=1);

namespace PeServer\Core\Errors;

use PeServer\Core\DI\Inject;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Http\IResponsePrinterFactory;
use PeServer\Core\Http\RequestPath;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Mvc\Template\ITemplateFactory;
use PeServer\Core\Mvc\Template\TemplateFactory;
use PeServer\Core\Mvc\Template\TemplateOptions;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\ProgramContext;
use PeServer\Core\Text;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Web\UrlHelper;
use PeServer\Core\Web\WebSecurity;
use Throwable;

class HttpErrorHandler extends ErrorHandler
{
	public function __construct(
		protected RequestPath $requestPath,
		ILogger $logger
	) {
		parent::__construct($logger);
	}

	#region ErrorHandler

	protected function registerImpl(): void
	{
		$isJson = Text::startsWith($this->requestPath->full, 'api/', true) || Text::startsWith($this->requestPath->full, 'ajax/', true);

		$whoops = new \Whoops\Run();
		$handler = $isJson
			? new \Whoops\Handler\JsonResponseHandler()
			: new \Whoops\Handler\PrettyPageHandler();
		if ($handler instanceof \Whoops\Handler\PrettyPageHandler) {
			$handler->setEditor('vscode');
		}
		$whoops->pushHandler($handler);
		$whoops->register();
	}

	#endregion
}
