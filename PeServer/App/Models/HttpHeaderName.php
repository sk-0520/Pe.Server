<?php

declare(strict_types=1);

namespace PeServer\App\Models;

abstract class HttpHeaderName
{
	#region define

	public const API_KEY = 'X-API-KEY';
	public const SECRET_KEY = 'X-SECRET-KEY';

	#endregion
}
