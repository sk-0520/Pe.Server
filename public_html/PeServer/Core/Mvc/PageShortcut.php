<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Mvc\PageShortcutKind;
use PeServer\Core\Throws\ArgumentException;

/**
 * ページャのショートカット。
 *
 * @immutable
 */
class PageShortcut
{
	#region variable
	#endregion

	/**
	 * 生成。
	 *
	 * @param int $pageNumber ページ番号(1基点)。
	 * @phpstan-param positive-int $pageNumber
	 * @param bool $current 自身が現在選択ページか。
	 * @param bool $enabled 有効か。
	 * @param PageShortcutKind $kind ショートカット種別。HTML(CSS) としてそのまま使用可能。
	 */
	public function __construct(
		public int $pageNumber,
		public bool $current,
		public bool $enabled,
		public PageShortcutKind $kind,
	) {
	}
}
