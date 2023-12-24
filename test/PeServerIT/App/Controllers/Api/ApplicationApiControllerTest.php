<?php

declare(strict_types=1);

namespace PeServerIT\App\Controllers\Api;

use PeServer\App\Models\Dao\Entities\PeSettingEntityDao;
use PeServer\Core\Http\HttpMethod;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Web\Url;
use PeServerTest\ItBody;
use PeServerTest\ItControllerClass;
use PeServerTest\ItMockStores;
use PeServerTest\ItOptions;
use PeServerTest\ItSetup;
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

	public function test_version_update()
	{
		// $options = new ItOptions(
		// 	stores: ItMockStores::none(),
		// );
		$actual = $this->call(HttpMethod::Get, '/api/application/version/update', setup: function (ItSetup $setup) {
			$peSettingEntityDao = new PeSettingEntityDao($setup->databaseContext);

			$peSettingEntityDao->updatePeSettingVersion("123");
		});

		$url = Url::parse("https://github.com/sk-0520/Pe/releases/download/123/update.json");
		$this->assertRedirectUrl(HttpStatus::Found, $url, $actual);
	}
}
