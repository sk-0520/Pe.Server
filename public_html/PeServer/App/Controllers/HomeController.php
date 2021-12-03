<?php declare(strict_types=1);
require_once('PeServer/App/Controllers/ControllerBase.php');
require_once('PeServer/App/Models/Home/HomeIndex.php');

class HomeController extends ControllerBase
{
	public function __construct(ControllerArguments $arguments)
	{
		parent::__construct($arguments);
	}

	public function index()
	{
		return $this->view('index');
	}
}
