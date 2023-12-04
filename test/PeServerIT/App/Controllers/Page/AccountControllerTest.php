<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Page;

use PeServer\App\Controllers\Page\HomeController;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;
use PeServerTest\ItSetupDatabaseTrait;
use PeServerTest\ItUseDatabase;
use PeServerTest\TestControllerClass;

class AccountControllerTest extends TestControllerClass
{
	use ItUseDatabase;

	public function test_index()
	{
		$actual = $this->call(HttpMethod::Get, '/account');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTitle('ログイン', $actual);

		$this->assertStatus(HttpStatus::OK, HttpMethod::Get, '/account');
	}

	public function test_login()
	{
		$actual = $this->call(HttpMethod::Get, '/account/login');
		$this->assertSame(HttpStatus::OK, $actual->getHttpStatus());
		$this->assertTitle('ログイン', $actual);
	}
}
