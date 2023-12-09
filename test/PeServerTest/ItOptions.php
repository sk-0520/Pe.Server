<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Http\HttpHeader;

class ItOptions
{
	public function __construct(
		public ItMockStores $stores = new ItMockStores(null, false),
		public ?HttpHeader $httpHeader = null,
		public ?ItBody $body = null
	) {
	}
}
