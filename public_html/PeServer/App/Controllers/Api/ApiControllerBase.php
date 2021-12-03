<?php
require_once('PeServer/App/Controllers/ControllerBase.php');

abstract class ApiControllerBase extends ControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}
}
