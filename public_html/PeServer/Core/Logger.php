<?php declare(strict_types=1);
namespace PeServer\Core;

require_once('PeServer/Core/ILogger.php');

class Logger implements ILogger
{
	private $header;

	public function __construct(string $header)
	{
		$this->header = $header;
	}
}
