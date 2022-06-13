<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

/**
 * ページャ。
 *
 * まだ使わんからどうしようもねぇなぁ。
 */
class Pager
{
	/**
	 * ページ内データ全件数。
	 *
	 * @var integer
	 */
	public int $pageCount;
	/**
	 * 全件数。
	 *
	 * @var integer
	 */
	public int $totalCount;
	/**
	 * 現在のページ番号(1基点)。
	 *
	 * @var integer
	 */
	public int $currentPageNumber;
	/**
	 * リンク表示件数。
	 *
	 * @var integer
	 */
	public int $shortcutCount = 5;

	public function __construct(int $pageCount, int $totalCount, int $currentPageNumber)
	{
		assert(0 < $currentPageNumber);

		$this->pageCount = $pageCount;
		$this->totalCount = $totalCount;
		$this->currentPageNumber = $currentPageNumber;
	}
}
