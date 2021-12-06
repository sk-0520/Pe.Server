<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;

class ActionRequest
{
	private $urlRequests;

	public function __construct(array $urlRequests)
	{
		$this->urlRequests  = $urlRequests;
	}

	public function exists(string $key): bool
	{
		//TODO: $urlRequests

		if(isset($_GET[$key])) {
			return true;
		}

		if(isset($_POST[$key])) {
			return true;
		}

		if(isset($_FILES[$key])) {
			return true;
		}

		return false;
	}

	// public function isMulti(string $key): bool
	// {

	// }

	public function get($key): mixed
	{
		//TODO: $urlRequests

		if(isset($_GET[$key])) {
			return $_GET[$key];
		}

		if(isset($_POST[$key])) {
			return $_POST[$key];
		}

		throw new Exception("parameter not found: $key");
	}

	// public function gets($key): array
	// {
	// }

	// public function file($key): array
	// {
	// }
}
