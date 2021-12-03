<?php

declare(strict_types=1);

namespace PeServer\App\Controllers;

use \PeServer\Core\ControllerArguments;
use \PeServer\App\Controllers\ControllerBase;

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
