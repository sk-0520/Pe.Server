<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * HTTPステータスコード。
 */
abstract class HttpStatusCode
{
	const DO_EXECUTE = 0;

	const OK = 200;
	const FORBIDDEN = 403;
	const NOT_FOUND = 404;
	const METHOD_NOT_ALLOWED = 405;
	const INTERNAL_SERVER_ERROR = 500;
	const SERVICE_UNAVAILABLE = 503;
}
