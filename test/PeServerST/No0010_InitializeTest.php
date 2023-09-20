<?php

declare(strict_types=1);

namespace PeServerST;

use PeServer\Core\Http\Client\HttpClient;
use PeServer\Core\Http\Client\HttpClientOptions;
use PeServer\Core\Web\Url;
use PeServerTest\TestClass;

class No0010_InitializeTest extends TestClass
{
	public function test() {
		$hc = new HttpClient(new HttpClientOptions());

		$initializeUrl = Url::parse(self::localServer('/api/development/initialize'));
		$hc->post($initializeUrl);

		$administratorUrl = Url::parse(self::localServer('/api/development/administrator'));
		$hc->post($administratorUrl);


		$this->success();
	}
}
