<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Binary;
use PeServer\Core\Database\IDatabaseConnection;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Http\ContentType;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServer\Core\Serialization\JsonSerializer;
use PeServerTest\ItHtmlDocument;

readonly class ItActual
{
	public ItHtmlDocument|null $html;
	public array|null $json;

	public function __construct(
		public HttpResponse $response,
		public IDiContainer $container,
	) {
		if ($this->isHtml()) {
			$this->html = ItHtmlDocument::new($this->response->content);
		} else {
			$this->html = null;

			if ($this->isJson()) {
				$serializer = new JsonSerializer();
				if (is_string($this->response->content)) {
					$this->json = $serializer->load(new Binary($this->response->content));
				} else if ($this->response->content instanceof Binary) {
					$this->json = $serializer->load($this->response->content);
				} else {
					$this->json = null;
				}
			}
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
