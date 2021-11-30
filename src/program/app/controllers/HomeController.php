<?php
require_once('program/app/controllers/ControllerBase.php');

class HomeController extends ControllerBase
{
	public function __construct(ControllerInput $input)
	{
		parent::__construct($input);
	}

	public function index()
	{
		return $this->view('index');
	}
}
