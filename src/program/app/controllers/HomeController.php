<?php
require_once('program/app/controllers/ControllerBase.php');

class HomeController extends ControllerBase
{
	public function index()
	{
		return $this->view('index');
	}
}
