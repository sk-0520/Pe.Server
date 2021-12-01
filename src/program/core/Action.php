<?php declare(strict_types=1);
require_once('program/core/HttpMethod.php');

class Action
{
	public $httpMethod;
	public $callMethod;

	public function __construct(string $httpMethod, string $callMethod)
	{
		$this->httpMethod = $httpMethod;
		$this->callMethod = $callMethod;
	}
}
