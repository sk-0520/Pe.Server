<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\HttpStatus;

class RedirectActionResult extends ActionResult
{
	public function __construct(string $url, HttpStatus $redirectStatus)
	{
		parent::__construct([
			'Location' => ['value' => $url, 'status' => $redirectStatus],
		]);
	}

	protected function body(): void
	{
		//NONE
	}
}
