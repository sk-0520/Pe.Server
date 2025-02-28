<?php

declare(strict_types=1);

namespace PeServer\Core\Store;

use SessionHandlerInterface;

interface ISessionHandlerFactory
{
	public function create(SessionOptions $options): SessionHandlerInterface;
}
