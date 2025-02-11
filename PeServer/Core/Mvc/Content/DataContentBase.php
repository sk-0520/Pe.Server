<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Content;

use PeServer\Core\Binary;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mime;

/**
 * アクション応答。
 *
 * 何かしらのデータを想定。
 */
abstract class DataContentBase
{
	/**
	 * 生成。
	 *
	 * @param HttpStatus $httpStatus 応答HTTPステータスコード。
	 * @param non-empty-string $mime MIME。Mime を参照のこと。
	 * @phpstan-param non-empty-string|\PeServer\Core\Mime::* $mime
	 */
	protected function __construct(
		public HttpStatus $httpStatus,
		public string $mime
	) {
	}
}
