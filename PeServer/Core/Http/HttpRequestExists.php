<?php

declare(strict_types=1);

namespace PeServer\Core\Http;

/**
 * HTTPリクエスト存在確認。
 */
readonly class HttpRequestExists
{
	#region define

	/** 使わん */
	public const KIND_NONE = 0;
	/** URL */
	public const KIND_URL = 1;
	/** GET */
	public const KIND_GET = 2;
	/** POST */
	public const KIND_POST = 3;
	/** FILE */
	public const KIND_FILE = 4;

	#endregion

	/**
	 * 生成。
	 *
	 * @param string $name パラメータ名。
	 * @param boolean $exists 存在するか。
	 * @param self::KIND_* $kind 種別。
	 */
	public function __construct(
		public string $name,
		public bool $exists,
		public int $kind
	) {
	}
}
