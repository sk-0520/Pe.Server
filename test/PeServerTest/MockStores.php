<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\App\Models\Data\SessionAccount;

class MockStores
{
	public function __construct(
		public SessionAccount|null $account = null
	) {
	}
}
