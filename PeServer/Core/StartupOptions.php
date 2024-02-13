<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * スタートアップオプション。
 */
readonly class StartupOptions
{
	/**
	 * 生成。
	 *
	 * @param string $root アプリケーションのルートディレクトリ($HOME/app とか)。
	 * @param string $public $homeから見た公開ディレクトリ相対パス。(Webアプリとして使用しない場合は空)。
	 * @param bool $errorHandling エラーハンドリングを設定するか。テストで制御する目的のため原則未指定で良い。
	 */
	public function __construct(
		public string $root,
		public string $public,
		public bool $errorHandling = true,
	) {
	}
}
