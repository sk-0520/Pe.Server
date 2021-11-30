<?php
require_once('program/app/controllers/api/ApiControllerBase.php');
require_once('program/app/models/api/Hello/HelloIndex.php');

class HelloController extends ApiControllerBase
{
	public function __construct(ControllerInput $input)
	{
		parent::__construct($input);
	}
}
