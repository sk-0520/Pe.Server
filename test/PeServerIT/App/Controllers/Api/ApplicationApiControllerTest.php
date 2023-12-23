<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Api;

use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServerTest\ItBody;
use PeServerTest\ItControllerClass;
use PeServerTest\ItMockStores;
use PeServerTest\ItOptions;
use PeServerTest\ItUseDatabaseTrait;

class ApplicationApiControllerTest extends ItControllerClass
{
	use ItUseDatabaseTrait;

	public function test_dummy()
	{
		$this->success();
	}

	// メールかぁ・・・・・
	// 	public function test_feedback()
	// 	{
	// 		$options = new ItOptions(
	// 			body: ItBody::json([
	// 				'kind' => ''
	// 			])
	// 		);
	// 		$actual = $this->call(HttpMethod::Get, '/api/application/feedback', $options);

	// 		$this->assertStatusOk($actual);
	// 	}
}
