<?php

declare(strict_types=1);

namespace PeServer\Core\Errors;

use PeServer\Core\Throws\InvalidOperationException;

readonly final class ErrorData
{
	public function __construct(
		public int $type,
		public string $message,
		public string $file,
		public int $line,
	) {
		//NOP
	}

	#region functino

	/**
	 * `get_last_error` から生成。
	 *
	 * @param array{type:int,message:string,file:string,line:int} $error
	 * @return self
	 */
	public static function createFromArray(array $error): self
	{
		return new self(
			$error["type"],
			$error["message"],
			$error["file"],
			$error["line"],
		);
	}

	public static function createFromLastError(): self
	{
		$result = self::getLastError();

		if ($result === null) {
			throw new InvalidOperationException();
		}

		return $result;
	}

	public static function getLastError(): self|null
	{
		$last = \error_get_last();

		if ($last === null) {
			return null;
		}

		return self::createFromArray($last);
	}

	#endregion
}
