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

	public static function none(): HttpStatus
	{
		return new _HttpStatus_Impl(0);
	}
	public static function create(int $code): HttpStatus
	{
		return new _HttpStatus_Impl($code);
	}

	public static function ok(): HttpStatus
	{
		return new _HttpStatus_Impl(200);
	}

	public static function found(): HttpStatus
	{
		return new _HttpStatus_Impl(302);
	}

	public static function forbidden(): HttpStatus
	{
		return new _HttpStatus_Impl(403);
	}
	public static function notFound(): HttpStatus
	{
		return new _HttpStatus_Impl(404);
	}
	public static function methodNotAllowed(): HttpStatus
	{
		return new _HttpStatus_Impl(405);
	}

	public static function internalServerError(): HttpStatus
	{
		return new _HttpStatus_Impl(500);
	}
	public static function serviceUnavailable(): HttpStatus
	{
		return new _HttpStatus_Impl(503);
	}

	public abstract function code(): int;
}

class _HttpStatus_Impl extends HttpStatus
{
	private int $code;

	public function __construct(int $code)
	{
		$this->code = $code;
	}

	public function code(): int
	{
		return $this->code;
	}
}
