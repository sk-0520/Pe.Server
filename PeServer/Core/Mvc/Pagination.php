<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Mvc\PageShortcut;
use PeServer\Core\Mvc\PageShortcutKind;
use PeServer\Core\Throws\ArgumentException;

/**
 * ページャ。
 *
 * @immutable
 */
class Pagination
{
	#region define

	/**
	 * ページ番号基点。
	 */
	public const FIRST_PAGE_NUMBER = 1;

	private const SHORTCUT_HEAD = 0;
	private const SHORTCUT_TAIL = 1;

	#endregion

	#region variable

	/**
	 * 全ショートカット数。
	 *
	 * 全てなので `$shortcutMaxCount` を超過する。
	 *
	 * @var int
	 */
	public int $shortcutTotalItemCount;

	#endregion

	/**
	 * 生成。
	 *
	 * @param int $currentPageNumber 現在ページ番号(1基点)
	 * @phpstan-param positive-int $currentPageNumber
	 * @param int $itemCountInPage ページ内アイテムの表示件数。
	 * @phpstan-param positive-int $itemCountInPage
	 * @param int $totalItemCount アイテム全件数。
	 * @phpstan-param non-negative-int $totalItemCount
	 * @param bool $shortJump 直近(前後)へのリンク表示。
	 * @param bool $longJump 全件数(最初と最後)へのリンク表示。
	 * @param int $shortcutMaxCount ショートカットリンク表示数。
	 * @phpstan-param non-negative-int $shortcutMaxCount ショートカットリンク表示数。
	 */
	public function __construct(
		public int $currentPageNumber,
		public int $itemCountInPage,
		public int $totalItemCount,
		public bool $shortJump = true,
		public bool $longJump = true,
		private int $shortcutMaxCount = 5
	) {
		if ($itemCountInPage < 0) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException('$itemCountInPage');
		}

		if (!$totalItemCount) {
			$this->currentPageNumber = self::FIRST_PAGE_NUMBER;
			$this->shortcutTotalItemCount = 0;
		} else {
			$this->shortcutTotalItemCount = (int)ceil($this->totalItemCount / $this->itemCountInPage);
			if ($this->shortcutTotalItemCount <= $this->currentPageNumber) {
				$this->currentPageNumber = $this->shortcutTotalItemCount; //@phpstan-ignore-line [DOCTYPE]
			} elseif (!$this->currentPageNumber) { //@phpstan-ignore-line [DOCTYPE]
				$this->currentPageNumber = self::FIRST_PAGE_NUMBER;
			}
		}
	}

	#region function

	/**
	 * 通常ショートカットのみを取得。
	 *
	 * TODO: 偶数処理が💩
	 *
	 * @return PageShortcut[]
	 */
	public function getPageNumbers(): array
	{
		if ($this->shortcutTotalItemCount <= 0) {
			return [];
		}

		/** @phpstan-var positive-int[] */
		$pageNumbers = [];

		if ($this->shortcutTotalItemCount <= $this->shortcutMaxCount) {
			// ショートカット全件がショートカット設定数以下は全件を指定する
			$pageNumbers = Arr::range(self::FIRST_PAGE_NUMBER, $this->shortcutTotalItemCount);
		} else {
			$beginWidth = (int)($this->shortcutMaxCount / 2);
			$endWidth = $this->shortcutMaxCount - $beginWidth;
			if ($this->currentPageNumber - $beginWidth < 1) {
				$pageNumbers = Arr::range(1, $this->shortcutMaxCount);
			} elseif ($this->shortcutTotalItemCount - $endWidth < $this->currentPageNumber) {
				$pageNumbers = Arr::range($this->shortcutTotalItemCount - $this->shortcutMaxCount + 1, $this->shortcutMaxCount);
			} else {
				$beginPageNumber = $this->currentPageNumber - (int)($this->shortcutMaxCount / 2);
				if ($this->currentPageNumber < $beginPageNumber) {
					$beginPageNumber -= (int)($this->shortcutMaxCount / 2);
				}

				$pageNumbers = Arr::range($beginPageNumber, $this->shortcutMaxCount);
			}
		}


		/** @var PageShortcut[] */
		$shortcuts = [];
		foreach ($pageNumbers as $pageNumber) {
			/** @phpstan-var positive-int $pageNumber */
			$item = new PageShortcut(
				$pageNumber,
				$this->currentPageNumber == $pageNumber,
				true,
				PageShortcutKind::Normal,
			);

			$shortcuts[] = $item;
		}

		return $shortcuts;
	}

	/**
	 * Undocumented function
	 *
	 * @param PageShortcut[] $pageShortcuts
	 * @return PageShortcut[]|null
	 */
	private function getShortShortcuts(array $pageShortcuts): ?array
	{
		if (!$this->shortJump) {
			return null;
		}

		if ($this->shortcutTotalItemCount === 0 || empty($pageShortcuts)) {
			return [
				self::SHORTCUT_HEAD => new PageShortcut(PHP_INT_MIN, false, false, PageShortcutKind::Short), //@phpstan-ignore-line このページ番号は使用しない
				self::SHORTCUT_TAIL => new PageShortcut(PHP_INT_MAX, false, false, PageShortcutKind::Short), // このページ番号は使用しない
			];
		}

		return [
			self::SHORTCUT_HEAD => new PageShortcut($this->currentPageNumber - 1, false, $this->currentPageNumber !== 1, PageShortcutKind::Short), //@phpstan-ignore-line 状況次第でこのページ番号は使用しない
			self::SHORTCUT_TAIL => new PageShortcut($this->currentPageNumber + 1, false, $this->currentPageNumber !== $this->shortcutTotalItemCount, PageShortcutKind::Short), //状況次第でこのページ番号は使用しない
		];
	}

	/**
	 * Undocumented function
	 *
	 * @return PageShortcut[]|null
	 */
	private function getLongShortcuts(): ?array
	{
		if (!$this->longJump) {
			return null;
		}

		if ($this->shortcutTotalItemCount === 0) {
			return [
				self::SHORTCUT_HEAD => new PageShortcut(PHP_INT_MIN, false, false, PageShortcutKind::Long), //@phpstan-ignore-line このページ番号は使用しない
				self::SHORTCUT_TAIL => new PageShortcut(PHP_INT_MAX, false, false, PageShortcutKind::Long), // このページ番号は使用しない
			];
		}

		return [
			self::SHORTCUT_HEAD => new PageShortcut(1, false, $this->currentPageNumber !== 1, PageShortcutKind::Long),
			self::SHORTCUT_TAIL => new PageShortcut($this->shortcutTotalItemCount, false, $this->currentPageNumber !== $this->shortcutTotalItemCount, PageShortcutKind::Long),  //@phpstan-ignore-line
		];
	}

	/**
	 * ページャのあれこれを返す。
	 *
	 * View側で回す想定。
	 *
	 * @return PageShortcut[]
	 */
	public function getShortcuts(): array
	{
		/** @var PageShortcut[] */
		$result = [];

		$numbers = $this->getPageNumbers();

		$long = $this->getLongShortcuts();
		$short = $this->getShortShortcuts($numbers);

		if ($long !== null) {
			$result[] = $long[self::SHORTCUT_HEAD];
		}
		if ($short !== null) {
			$result[] = $short[self::SHORTCUT_HEAD];
		}

		foreach ($numbers as $number) {
			$result[] = $number;
		}
		//$result += $numbers;

		if ($short !== null) {
			$result[] = $short[self::SHORTCUT_TAIL];
		}
		if ($long !== null) {
			$result[] = $long[self::SHORTCUT_TAIL];
		}

		return $result;
	}

	#endregion
}
