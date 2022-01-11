<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Bytes;
use PeServer\Core\Http\HttpStatus;

/**
 * アクション応答。
 *
 * JSONやらのデータを想定。
 */
class DataContent
{
	/**
	 * 応答HTTPステータスコード。
	 */
	public HttpStatus $httpStatus;
	/**
	 * MIME
	 *
	 * Mime を参照のこと。
	 */
	public string $mime;
	/**
	 * 応答生データ。
	 *
	 * このデータ自体はプログラム側の生値で保持する。
	 *
	 * @var string|array<mixed>|Bytes
	 */
	public $data;

	/**
	 * 生成。
	 *
	 * @param HttpStatus $httpStatus
	 * @param string $mime
	 * @param string|array<mixed>|Bytes $data
	 */
	public function __construct(HttpStatus $httpStatus, string $mime, $data)
	{
		$this->httpStatus = $httpStatus;
		$this->mime = $mime;
		$this->data = $data;
	}

	// /**
	//  * プレーンテキスト応答。
	//  *
	//  * @param string $data
	//  * @param HttpStatus|null $httpStatus
	//  * @return ActionResponse
	//  */
	// public static function text(string $data, ?HttpStatus $httpStatus = null): ActionResponse
	// {
	// 	return new ActionResponse($httpStatus ?? HttpStatus::ok(), Mime::TEXT, $data);
	// }

	// /**
	//  * JSON応答。
	//  *
	//  * @param array<mixed> $data
	//  * @param HttpStatus|null $httpStatus
	//  * @return ActionResponse
	//  */
	// public static function json(array $data, ?HttpStatus $httpStatus = null): ActionResponse
	// {
	// 	return new ActionResponse($httpStatus ?? HttpStatus::ok(), Mime::JSON, $data);
	// }
}
