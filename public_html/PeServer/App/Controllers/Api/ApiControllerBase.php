<?php
namespace PeServer\App\Controllers\Api;

use \PeServer\Core\ControllerArguments;
use PeServer\App\Controllers\ControllerBase;

require_once('PeServer/App/Controllers/ControllerBase.php');

abstract class ApiControllerBase extends ControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}
}
