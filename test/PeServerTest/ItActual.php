<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Http\ContentType;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServerTest\ItHtmlDocument;

readonly class ItActual
{
	public ItHtmlDocument|null $html;

	public function __construct(
		public HttpResponse $response,
		public IDiContainer $container,
	) {
		if ($this->isHtml()) {
			$this->html = ItHtmlDocument::new($this->response->content);
		} else {
			$this->html = null;
		}
	}


	#region

	public function getHttpStatus(): HttpStatus
	{
		return $this->response->status;
	}

	public function getContentType(): ContentType
	{
		return $this->response->header->getContentType();
	}

	public function isHtml(): bool
	{
		return $this->response->header->existsContentType() && $this->getContentType()->mime === Mime::HTML;
	}

	public function isJson(): bool
	{
		return $this->response->header->existsContentType() && $this->getContentType()->mime === Mime::JSON;
	}

	public function openDB(): IDatabaseContext
	{
		/** @var IDatabaseConnection */
		$database = $this->container->get(IDatabaseConnection::class);

		return $database->open();
	}

	#endregion
}
