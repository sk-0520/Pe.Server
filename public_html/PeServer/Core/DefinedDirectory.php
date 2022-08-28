<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * 最低限のディレクトリ定義。
 *
 * @immutable
 */
class DefinedDirectory
{
	#region variable

	/**
	 * ライブラリディレクトリパス。
	 *
	 * @var string
	 * @phpstan-var non-empty-string
	 */
	public string $core;

	#endregion

	/**
	 * 生成。
	 *
	 * @param string $application アプリケーションのディレクトリパス(アプリケーションが無ければ空)。
	 * @param string $public 公開ディレクトリパス。(Webアプリとして使用しない場合は空)。
	 */
	public function __construct(
		public string $application,
		public string $public
	) {
		$this->core = __DIR__;
	}
}
