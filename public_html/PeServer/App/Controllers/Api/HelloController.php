<?php
require_once('PeServer/App/Controllers/Api/ApiControllerBase.php');
require_once('PeServer/App/Models/Api/Hello/HelloIndex.php');

class HelloController extends ApiControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}
}
