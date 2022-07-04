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
		return new LocalHttpStatusImpl(0);
	}

	public static function create(int $code): HttpStatus
	{
		return new LocalHttpStatusImpl($code);
	}

	public static function ok(): HttpStatus
	{
		return new LocalHttpStatusImpl(200);
	}

	public static function moved(): HttpStatus
	{
		return new LocalHttpStatusImpl(301);
	}
	public static function found(): HttpStatus
	{
		return new LocalHttpStatusImpl(302);
	}

	public static function badRequest(): HttpStatus
	{
		return new LocalHttpStatusImpl(400);
	}
	public static function authorizationRequired(): HttpStatus
	{
		return new LocalHttpStatusImpl(401);
	}
	public static function forbidden(): HttpStatus
	{
		return new LocalHttpStatusImpl(403);
	}
	public static function notFound(): HttpStatus
	{
		return new LocalHttpStatusImpl(404);
	}
	public static function methodNotAllowed(): HttpStatus
	{
		return new LocalHttpStatusImpl(405);
	}
	public static function misdirected(): HttpStatus
	{
		return new LocalHttpStatusImpl(421);
	}


	public static function internalServerError(): HttpStatus
	{
		return new LocalHttpStatusImpl(500);
	}
	public static function serviceUnavailable(): HttpStatus
	{
		return new LocalHttpStatusImpl(503);
	}

	/**
	 * HTTPステータスコードを取得。
	 *
	 * @return integer
	 */
	public abstract function getCode(): int;

	/**
	 * 指定ステータスコードオブジェクトが自身と同じか。
	 *
	 * @param HttpStatus $httpStatus
	 * @return boolean
	 */
	public abstract function is(HttpStatus $httpStatus): bool;
}

class LocalHttpStatusImpl extends HttpStatus
{
	public function __construct(
		/** @readonly */
		private int $code
	) {
	}

	public function getCode(): int
	{
		return $this->code;
	}

	public function is(HttpStatus $httpStatus): bool
	{
		if ($httpStatus instanceof LocalHttpStatusImpl) {
			return $this->code === $httpStatus->code;
		}

		return $this->code === $httpStatus->getCode();
	}
}
