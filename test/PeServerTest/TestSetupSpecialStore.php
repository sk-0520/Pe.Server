<?php

declare(strict_types=1);

namespace PeServerTest;

use PeServer\Core\Http\HttpHeader;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Text;

final class TestSetupSpecialStore extends SpecialStore
{
	public function getServer(string $name, mixed $fallbackValue = Text::EMPTY): mixed
	{
		switch ($name) {
			case 'REQUEST_METHOD':
				return 'GET';

			default:
				break;
		}

		return parent::getServer($name, $fallbackValue);
	}

	public function getRequestHeader(): HttpHeader
	{
		return new HttpHeader();
	}
}
