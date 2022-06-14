<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

/**
 * HTTPステータスコード。
 */
abstract class HttpStatus
{
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

	public static function badRequest(): HttpStatus
	{
		return new _HttpStatus_Impl(400);
	}
	public static function authorizationRequired(): HttpStatus
	{
		return new _HttpStatus_Impl(401);
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
	public static function misdirected(): HttpStatus
	{
		return new _HttpStatus_Impl(421);
	}


	public static function internalServerError(): HttpStatus
	{
		return new _HttpStatus_Impl(500);
	}
	public static function serviceUnavailable(): HttpStatus
	{
		return new _HttpStatus_Impl(503);
	}

	public abstract function getCode(): int;

	public abstract function is(HttpStatus $httpStatus): bool;
}

class _HttpStatus_Impl extends HttpStatus
{
	public int $code;

	public function __construct(int $code)
	{
		$this->code = $code;
	}

	public function getCode(): int
	{
		return $this->code;
	}

	public function is(HttpStatus $httpStatus): bool
	{
		if ($httpStatus instanceof _HttpStatus_Impl) {
			return $this->code === $httpStatus->code;
		}

		return $this->code === $httpStatus->getCode();
	}
}
