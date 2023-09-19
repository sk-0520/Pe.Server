<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use stdClass;
use PeServer\Core\Throws\ArgumentNullException;

/**
 * API/AJAX の共通応答データ。
 */
class ResponseJson
{
	#region variable

	/**
	 * 正常系データ。
	 *
	 * @var mixed
	 */
	public mixed $data;
	/**
	 * 異常系データ。
	 *
	 * @var array{message:string,code:string,info:mixed}|null
	 */
	public ?array $error = null;

	#endregion

	#region function

	/**
	 * 正常データ生成。
	 *
	 * @param mixed $data
	 * @return ResponseJson
	 */
	public static function success(mixed $data): ResponseJson
	{
		if ($data === null) {
			throw new ArgumentNullException('$data');
		}

		$result = new ResponseJson();
		$result->data = $data;

		return $result;
	}

	/**
	 * 異常データ生成。
	 *
	 * @param string $message
	 * @param string $code
	 * @param mixed $info
	 * @return ResponseJson
	 */
	public static function error(string $message, string $code, mixed $info): ResponseJson
	{
		$result = new ResponseJson();
		$result->data = new stdClass();
		$result->error = [
			'message' => $message,
			'code' => $code,
			'info' => $info,
		];

		return $result;
	}

	#endregion
}
