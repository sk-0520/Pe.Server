<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

/**
 * HTTPリクエスト存在確認。
 * @immutable
 */
class HttpRequestExists
{
	public const KIND_NONE = 0;
	public const KIND_URL = 1;
	public const KIND_GET = 2;
	public const KIND_POST = 3;
	public const KIND_FILE = 4;

	/**
	 * 生成。
	 *
	 * @param string $name パラメータ名。
	 * @param boolean $exists 存在するか。
	 * @param integer $kind 種別。
	 * @phpstan-param HttpRequestExists::KIND_* $kind 種別。
	 */
	public function __construct(
		public string $name,
		public bool $exists,
		public int $kind
	) {
	}
}
