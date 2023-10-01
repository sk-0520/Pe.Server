<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\App\Controllers\Page\HomeController;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServerTest\TestControllerClass;

class HomeControllerTest extends TestControllerClass
{
	public function test_index()
	{
		$actual = $this->call(HttpMethod::Get, '');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());

		$this->assertStatus(HttpStatus::OK, HttpMethod::Get, '/');
	}

	public function test_not_found()
	{
		$this->assertStatus(HttpStatus::NotFound, HttpMethod::Get, '/not');
	}
}
