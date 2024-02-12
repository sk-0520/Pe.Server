<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * 最低限のディレクトリ定義。
 */
readonly class DefinedDirectory
{
	/**
	 * 生成。
	 *
	 * @param string $root アプリケーションのルートディレクトリ($HOME/app とか)。
	 * @param string $public $homeから見た公開ディレクトリ相対パス。(Webアプリとして使用しない場合は空)。
	 */
	public function __construct(
		public string $root,
		public string $public
	) {
	}
}
