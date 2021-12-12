<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use LogicException;

/**
 * アクション応答。
 *
 * 通常のView表示以外で使用する。
 */
class ActionResponse
{
	/**
	 * 応答HTTPステータスコード。
	 *
	 * HttpStatusCode を参照のこと。
	 *
	 * @var int
	 */
	public $httpStatusCode;
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
	 * @var callback|null
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

	public function __construct(int $httpStatusCode, string $mime, $data, ?callable $callback = null)
	{
		$this->httpStatusCode = $httpStatusCode;
		$this->mime = $mime;
		$this->data = $data;
		$this->callback = $callback;
	}

	/**
	 * プレーンテキスト応答。
	 *
	 * @param string $data
	 * @param int $httpStatusCode
	 * @return ActionResponse
	 */
	public static function text(string $data, int $httpStatusCode = HttpStatusCode::OK): ActionResponse
	{
		return new ActionResponse($httpStatusCode, Mime::TEXT_PLAIN, $data);
	}

	/**
	 * JSON応答。
	 *
	 * @param array $data
	 * @param int $httpStatusCode
	 * @return ActionResponse
	 */
	public static function json(array $data, int $httpStatusCode = HttpStatusCode::OK): ActionResponse
	{
		return new ActionResponse($httpStatusCode, Mime::JSON, $data);
	}
}
