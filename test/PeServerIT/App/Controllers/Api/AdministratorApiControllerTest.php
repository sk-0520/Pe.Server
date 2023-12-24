<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Api;

use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServerTest\ItControllerClass;
use PeServerTest\ItMockStores;
use PeServerTest\ItOptions;
use PeServerTest\ItUseDatabaseTrait;

class AdministratorApiControllerTest extends ItControllerClass
{
	use ItUseDatabaseTrait;

	public function test_dummy()
	{
		$this->success();
	}

	//TODO: APIのメソッド周りはなにか統一したほうがいいなぁ

	// TODO: DBはメモリ上にある、以上
	// public function test_backup()
	// {
	// 	$actual = $this->call(HttpMethod::Post, '/api/administrator/backup');

	// 	$this->assertStatus(HttpStatus::MethodNotAllowed, $actual);
	// }
}
