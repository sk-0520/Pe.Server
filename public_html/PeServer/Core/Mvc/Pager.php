<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

class Pager
{
	/**
	 * ページ内データ件数。
	 *
	 * @var integer
	 */
	public int $pageCount;
	/**
	 * 全体件数。
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
	public int $pageNumberCount = 5;

	public function __construct(int $pageCount, int $totalCount, int $currentPageNumber)
	{
		assert(0 < $currentPageNumber);

		$this->pageCount = $pageCount;
		$this->totalCount = $totalCount;
		$this->currentPageNumber = $currentPageNumber;
	}
}
