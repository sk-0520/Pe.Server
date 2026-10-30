<?php

declare(strict_types=1);

namespace PeServer\App\Views\Components\Asset;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\Rel;
use PeServer\Core\Mvc\Template\Node\NodeContextBase;
use PeServer\Core\Mvc\Template\Node\Props;

readonly class AssetProps extends Props
{
	public function __construct(
		public string $file,
		public bool|null $autoSize = null,
		/**
		 * インラインデータとして扱うか。
		 */
		public bool|null $include = null,
		/**
		 * 公開ディレクトリを基準にするか。
		 *
		 * 基本的に `$include` が false であれば HTTP 上のパスを使用するのでこのプロパティを使用する必要はないが通信を抑える必要がある場合に使用する。
		 */
		public bool|null $includeIsPublic = null,
		public Rel|null $rel = null,
		// img
		public string|null $alt = null,
		public int|null $width = null,
		public int|null $height = null,
	) {
		//NOP
	}
}
