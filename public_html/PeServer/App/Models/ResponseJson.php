<?php

declare(strict_types=1);

namespace PeServer\App\Models;

use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Template;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\Throws\ArgumentNullException;
use stdClass;


/**
 * API/AJAX の共通応答データ。
 */
class ResponseJson
{
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
	public ?array $error;

	/**
	 * 正常データ生成。
	 *
	 * @param mixed $data
	 * @return ResponseJson
	 */
	public static function success(mixed $data): ResponseJson
	{
		if (is_null($data)) {
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
}
