<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Binary;
use PeServer\Core\Http\HttpStatus;

/**
 * アクション応答。
 *
 * JSONやらのデータを想定。
 */
class DataContent
{
	/**
	 * 生成。
	 *
	 * @param HttpStatus $httpStatus 応答HTTPステータスコード。
	 * @param string $mime MIME。Mime を参照のこと。
	 * @param string|array<mixed>|Binary $data 応答生データ。このデータ自体はプログラム側の生値で保持する。
	 */
	public function __construct(
		public HttpStatus $httpStatus,
		public string $mime,
		public $data
	) {
	}
}
