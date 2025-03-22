<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Api;

use PeServer\App\Models\Dao\Entities\PeSettingEntityDao;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServerTest\ItBody;
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

	public function test_pe_version()
	{
		$actual = $this->call(HttpMethod::Post, '/api/administrator/pe/version', options: new ItOptions(
			body: ItBody::json([
				"version" => "1.2.3"
			])
		));

		$this->assertStatus(HttpStatus::OK, $actual);

		$context = $actual->openDB();
		$peSettingEntityDao = new PeSettingEntityDao($context);
		$actualVersion = $peSettingEntityDao->selectPeSettingVersion();
		$this->assertSame("1.2.3", $actualVersion);
	}
}
