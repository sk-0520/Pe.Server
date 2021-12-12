<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * HTTPステータスコード。
 */
class HttpStatusCode
{
	const OK = 200;
	const NOT_FOUND = 404;
	const INTERNAL_SERVER_ERROR = 500;
	const SERVICE_UNAVAILABLE = 503;
}
