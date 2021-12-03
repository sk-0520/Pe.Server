<?php declare(strict_types=1);
namespace PeServer\Core;

require_once('PeServer/Core/HttpMethod.php');

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
