<?php
require_once('program/app/controllers/ControllerBase.php');

abstract class ApiControllerBase extends ControllerBase
{
	public function __construct(ControllerInput $input)
	{
		parent::__construct($input);
	}
}
