<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * パス設定的な。
 */
readonly class ProgramContext
{
	/**
	 * 生成。
	 *
	 * @param string $rootDirectory ルートディレクトリ。
	 * @param string $applicationDirectory アプリケーションディレクトリ。`PeServer\*` を指すアプリコードの格納ルートディレクトリ。
	 * @param string $publicDirectory 公開ディレクトリ。Webルート。
	 */
	public function __construct(
		public string $rootDirectory,
		public string $applicationDirectory,
		public string $publicDirectory,
	) {
	}
}
