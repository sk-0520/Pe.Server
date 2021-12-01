<?php declare(strict_types=1);
require_once('program/app/controllers/ControllerBase.php');
require_once('program/app/models/Home/HomeIndex.php');

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
