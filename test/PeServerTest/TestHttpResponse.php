<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Http\ContentType;
use PeServer\Core\Http\HttpResponse;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServerTest\TestHtmlDocument;

class TestHttpResponse
{
	public readonly TestHtmlDocument|null $html;

	public function __construct(
		public HttpResponse $response
	) {
		if ($this->isHtml()) {
			$this->html = TestHtmlDocument::new($this->response->content);
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
		return $this->getContentType()->mime === Mime::HTML;
	}

	public function isJson(): bool
	{
		return $this->getContentType()->mime === Mime::JSON;
	}

	#endregion
}
