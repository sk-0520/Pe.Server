<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Binary;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;

/**
 * アクション応答。
 *
 * JSONやらのデータを想定。
 */
readonly class DataContent
{
	/**
	 * 生成。
	 *
	 * @param HttpStatus $httpStatus 応答HTTPステータスコード。
	 * @param non-empty-string $mime MIME。Mime を参照のこと。
	 * @phpstan-param non-empty-string|\PeServer\Core\Mime::* $mime
	 * @param string|array<mixed>|Binary $data 応答生データ。このデータ自体はプログラム側の生値で保持する。
	 */
	public function __construct(
		public HttpStatus $httpStatus,
		public string $mime,
		public string|array|Binary $data
	) {
	}
}
