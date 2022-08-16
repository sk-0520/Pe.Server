<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Throws\ArgumentException;

/**
 * ページャ。
 *
 * @phpstan-type ShortcutAlias array{page:int,current:bool,enabled:bool,shortcut:'normal'|'short'|'long'}
 * @immutable
 */
class Pagination
{
	/**
	 * 全ショートカット数
	 *
	 * @var int
	 */
	public int $shortcutTotalCount;

	/**
	 * 生成。
	 *
	 * @param int $currentPageNumber 現在ページ番号(1基点)
	 * @param int $itemCount ページ内の表示件数。
	 * @param int $totalCount 全件数。
	 * @param bool $shortJump 直近(前後)へのリンク表示。
	 * @param bool $longJump 全件数(最初と最後)へのリンク表示。
	 * @param int $shortcutMaxCount ショートカットリンク表示数。
	 */
	public function __construct(
		public int $currentPageNumber,
		public int $itemCount,
		public int $totalCount,
		public bool $shortJump = true,
		public bool $longJump = true,
		private int $shortcutMaxCount = 5
	) {
		if ($itemCount < 0) {
			throw new ArgumentException('$itemCount');
		}

		if (!$totalCount) {
			$this->currentPageNumber = 1;
			$this->shortcutTotalCount = 0;
		} else {
			$this->shortcutTotalCount = (int)ceil($this->totalCount / $this->itemCount); //@phpstan-ignore-line @immutable
			if ($this->shortcutTotalCount <= $this->currentPageNumber) {
				$this->currentPageNumber = $this->shortcutTotalCount;
			}
		}
	}

	/**
	 * Undocumented function
	 *
	 * @phpstan-return array<ShortcutAlias>
	 */
	public function getShortcuts(): array
	{
		if ($this->shortcutTotalCount <= $this->shortcutMaxCount) {
			return range(1, $this->shortcutTotalCount); //@phpstan-ignore-line 一時的
		}

		return range(1, $this->shortcutMaxCount); //@phpstan-ignore-line 一時的
	}

	/**
	 * ページャのあれこれを返す。
	 *
	 * View側で回す想定。
	 *
	 * @phpstan-return array<ShortcutAlias>
	 */
	public function getPageNumbers(): array
	{
		return [];
	}
}
