<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Content;

use PeServer\Core\Binary;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\IO\Stream;
use PeServer\Core\Mime;

/**
 * アクション応答。
 *
 * あらかじめ内容が固定されたデータを想定。
 */
class StaticDataContent extends DataContentBase
{
	/**
	 * 生成。
	 *
	 * @param HttpStatus $httpStatus 応答HTTPステータスコード。
	 * @param non-empty-string $mime MIME。Mime を参照のこと。
	 * @phpstan-param non-empty-string|\PeServer\Core\Mime::* $mime
	 * @param string|array<mixed>|Binary|Stream $data 応答生データ。このデータ自体はプログラム側の生値で保持する。
	 */
	public function __construct(
		HttpStatus $httpStatus,
		string $mime,
		public string|array|Binary|Stream $data
	) {
		parent::__construct($httpStatus, $mime);
	}
}
