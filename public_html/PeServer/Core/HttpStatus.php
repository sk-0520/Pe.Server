<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * HTTPステータスコード。
 */
abstract class HttpStatus
{
	// const DO_EXECUTE = 0;

	// const OK = 200;
	// const FORBIDDEN = 403;
	// const NOT_FOUND = 404;
	// const METHOD_NOT_ALLOWED = 405;
	// const INTERNAL_SERVER_ERROR = 500;
	// const SERVICE_UNAVAILABLE = 503;

	public static function doExecute(): HttpStatus
	{
		return new _HttpStatus_Invisible(0);
	}
	public static function ok(): HttpStatus
	{
		return new _HttpStatus_Invisible(200);
	}

	public static function forbidden(): HttpStatus
	{
		return new _HttpStatus_Invisible(403);
	}
	public static function notFound(): HttpStatus
	{
		return new _HttpStatus_Invisible(404);
	}
	public static function methodNotAllowed(): HttpStatus
	{
		return new _HttpStatus_Invisible(405);
	}

	public static function internalServerError(): HttpStatus
	{
		return new _HttpStatus_Invisible(500);
	}
	public static function serviceUnavailable(): HttpStatus
	{
		return new _HttpStatus_Invisible(503);
	}

	public abstract function code(): int;
}

class _HttpStatus_Invisible extends HttpStatus
{
	private int $_code;

	public function __construct(int $code)
	{
		$this->_code = $code;
	}

	public function code(): int
	{
		return $this->_code;
	}
}
