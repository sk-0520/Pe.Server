<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\ResponseOutput;
use \PeServer\Core\Mvc\IActionResult;


class DataActionResult extends ActionResult
{
	private ActionResponse $response;

	public function __construct(ActionResponse $response, array $headers)
	{
		parent::__construct($headers);

		$this->response = $response;
	}

	protected function header(): void
	{
		parent::header();

		header('Content-Type: ' . $this->response->mime);
		if ($this->response->chunked) {
			header("Transfer-encoding: chunked");
		}
	}

	protected function body(): void
	{
		if (is_null($this->response->callback)) {
			$converter = new ResponseOutput();
			$converter->output($this->response->mime, $this->response->chunked, $this->response->data);
		} else {
			call_user_func_array($this->response->callback, [$this->response->mime, $this->response->chunked, $this->response->data]);
		}
	}
}
