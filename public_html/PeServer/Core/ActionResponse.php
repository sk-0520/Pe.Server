<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use LogicException;

class ActionResponse
{
	public $httpStatusCode;
	public $mime;
	public $data;
	public $callback;

	public $chunked = false;

	public function __construct(int $httpStatusCode, string $mime, $data, ?callable $callback = null)
	{
		$this->httpStatusCode = $httpStatusCode;
		$this->mime = $mime;
		$this->data = $data;
		$this->callback = $callback;
	}
}
