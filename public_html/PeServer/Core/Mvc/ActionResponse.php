<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \Exception;
use \LogicException;
use \PeServer\Core\Mime;
use \PeServer\Core\HttpStatus;

/**
 * アクション応答。
 *
 * 通常のView表示以外で使用する。
 */
class ActionResponse
{
	/**
	 * 応答HTTPステータスコード。
	 */
	public HttpStatus $httpStatus;
	/**
	 * MIME
	 *
	 * Mime を参照のこと。
	 *
	 * @var string
	 */
	public $mime;
	/**
	 * 応答生データ。
	 *
	 * このデータ自体はプログラム側の生値で保持する。
	 *
	 * @var mixed
	 */
	public $data;
	/**
	 * 変換処理。
	 *
	 * $data を実際に応答データに変換する処理。
	 * null 設定時は標準処理が使用される。
	 *
	 * @var callable|null
	 */
	public $callback;

	/**
	 * 分割応答を行うか。
	 *
	 * あくまでチャンクである旨をヘッダにのせるだけの役割なのでPHP側にぶん投げかなぁ。
	 *
	 * @var boolean
	 */
	public $chunked = false;

	/**
	 * 生成。
	 *
	 * @param HttpStatus $httpStatus
	 * @param string $mime
	 * @param mixed $data
	 * @param callable|null $callback
	 */
	public function __construct(HttpStatus $httpStatus, string $mime, $data, ?callable $callback = null)
	{
		$this->httpStatus = $httpStatus;
		$this->mime = $mime;
		$this->data = $data;
		$this->callback = $callback;
	}

	/**
	 * プレーンテキスト応答。
	 *
	 * @param string $data
	 * @param HttpStatus|null $httpStatus
	 * @return ActionResponse
	 */
	public static function text(string $data, ?HttpStatus $httpStatus = null): ActionResponse
	{
		return new ActionResponse($httpStatus ?? HttpStatus::ok(), Mime::TEXT, $data);
	}

	/**
	 * JSON応答。
	 *
	 * @param array<mixed> $data
	 * @param HttpStatus|null $httpStatus
	 * @return ActionResponse
	 */
	public static function json(array $data, ?HttpStatus $httpStatus = null): ActionResponse
	{
		return new ActionResponse($httpStatus ?? HttpStatus::ok(), Mime::JSON, $data);
	}
}
