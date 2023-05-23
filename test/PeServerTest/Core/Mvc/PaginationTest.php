<?php

declare(strict_types=1);

namespace PeServerTest\Core\Mvc;

use PeServer\Core\Collections\Arr;
use PeServer\Core\Mvc\PageShortcut;
use PeServer\Core\Mvc\PageShortcutKind;
use PeServer\Core\Mvc\Pagination;
use PeServer\Core\Throws\ArgumentException;
use PeServerTest\TestClass;


class PaginationTest extends TestClass
{
	public function test_constructor_throw()
	{
		$this->expectException(ArgumentException::class);
		new Pagination(0, -1, 0);
		$this->fail();
	}

	public static function provider_constructor_shortcutTotalItemCount()
	{
		return [
			[0, 0, 0],
			[0, 1, 0],
			[1, 1, 1],
			[2, 1, 2],
			[1, 10, 10],
			[2, 10, 20],
			[1, 10, 9],
			[0, 10, 0],
			[1, 10, 1],
			[2, 10, 11],
			[2, 10, 19],
			[2, 10, 20],
			[3, 10, 21],
		];
	}
	/** @dataProvider provider_constructor_shortcutTotalItemCount */
	public function test_constructor_shortcutTotalItemCount($expected, $itemCountInPage, $totalItemCount)
	{
		$pager = new Pagination(0, $itemCountInPage, $totalItemCount);
		$this->assertSame($expected, $pager->shortcutTotalItemCount);
	}

	public function test_constructor_currentPageNumber()
	{
		$pager1 = new Pagination(0, 5, 10);
		$this->assertSame(1, $pager1->currentPageNumber);

		$pager2 = new Pagination(3, 5, 10);
		$this->assertSame(2, $pager2->currentPageNumber);
	}


	public static function provider_getPageNumbers_count()
	{
		return [
			// ショートカット数, 現在ページ, ページ内アイテム件数, アイテム全件数, ショートカット表示件数
			[1, 0, 1, 1, 1],
			[10, 0, 1, 10, 10],
			[5, 0, 2, 10, 5],
			[4, 0, 3, 10, 5],
			[1, 1, 3, 3, 3],
			[2, 1, 3, 6, 3],
			[3, 1, 3, 9, 3],
			[3, 1, 3, 12, 3],
		];
	}

	/** @dataProvider provider_getPageNumbers_count */
	public function test_getPageNumbers_count($expected, $currentPageNumber, $itemCountInPage, $totalItemCount, $shortcutMaxCount)
	{
		$pager = new Pagination($currentPageNumber, $itemCountInPage, $totalItemCount, shortcutMaxCount: $shortcutMaxCount);
		$this->assertSame($expected, Arr::getCount($pager->getPageNumbers()));
	}

	public static function provider_getPageNumbers_pageNumber()
	{
		return [
			// ショートカット一覧, 現在ページ, ページ内アイテム件数, アイテム全件数, ショートカット表示件数
			[[1], 0, 1, 1, 1],
			[[1], 1, 1, 1, 1],

			[[], 1, 1, 0, 1],

			[[1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 0, 1, 10, 10],
			[[1, 2, 3, 4, 5, 6, 7, 8, 9, 10], 1, 1, 10, 10],

			[[], 1, 1, 0, 10],

			[[1, 2], 0, 5, 10, 3],
			[[1, 2], 1, 5, 10, 3],

			[[1, 2, 3], 1, 5, 11, 3],
			[[1, 2, 3], 1, 5, 14, 3],
			[[1, 2, 3], 1, 5, 15, 3],

			// 先頭表示
			[[1, 2, 3], -1, 5, 30, 3],
			[[1, 2, 3], 0, 5, 30, 3],
			[[1, 2, 3], 1, 5, 30, 3],
			// 中間表示
			[[1, 2, 3], 2, 5, 30, 3],
			[[2, 3, 4], 3, 5, 30, 3],
			[[3, 4, 5], 4, 5, 30, 3],
			[[4, 5, 6], 5, 5, 30, 3],
			// 終端表示
			[[4, 5, 6], 6, 5, 30, 3],
			[[4, 5, 6], 7, 5, 30, 3],
			[[4, 5, 6], 8, 5, 30, 3],
			[[4, 5, 6], 9, 5, 30, 3],
			[[4, 5, 6], 10, 5, 30, 3],

			// 偶数 先頭表示(ミスったなぁこれ、右の方が多くあるべきだろ)
			[[1, 2, 3, 4], -1, 4, 32, 4],
			[[1, 2, 3, 4], 0, 4, 32, 4],
			[[1, 2, 3, 4], 1, 4, 32, 4],
			[[1, 2, 3, 4], 2, 4, 32, 4],
			[[1, 2, 3, 4], 3, 4, 32, 4],
			// 偶数 中間表示
			[[2, 3, 4, 5], 4, 4, 32, 4],
			[[3, 4, 5, 6], 5, 4, 32, 4],
			[[4, 5, 6, 7], 6, 4, 32, 4],
			[[5, 6, 7, 8], 7, 4, 32, 4],
			// 偶数 終端表示
			[[5, 6, 7, 8], 8, 4, 32, 4],
			[[5, 6, 7, 8], 9, 4, 32, 4],
		];
	}

	/** @dataProvider provider_getPageNumbers_pageNumber */
	public function test_getPageNumbers_pageNumber($expected, $currentPageNumber, $itemCountInPage, $totalItemCount, $shortcutMaxCount)
	{
		$pager = new Pagination($currentPageNumber, $itemCountInPage, $totalItemCount, shortcutMaxCount: $shortcutMaxCount);
		$shortcuts = $pager->getPageNumbers();
		$this->assertSame(Arr::getCount($expected), Arr::getCount($shortcuts));
		for ($i = 0; $i < count($expected); $i++) {
			$e = $expected[$i];
			$s = $shortcuts[$i];
			$this->assertSame($e, $s->pageNumber);
			$this->assertTrue($s->enabled);
			$this->assertSame($s->kind, PageShortcutKind::Normal);
			$this->assertSame($s->pageNumber === $pager->currentPageNumber, $s->current);
		}
	}

	public function test_getShortcuts()
	{
		$pager = new Pagination(5, 10, 100, true, true, 3);
		$actual = $pager->getShortcuts();

		$this->assertSame(PageShortcutKind::Long, $actual[0]->kind);
		$this->assertSame(1, $actual[0]->pageNumber);
		$this->assertTrue($actual[0]->enabled);
		$this->assertFalse($actual[0]->current);

		$this->assertSame(PageShortcutKind::Short, $actual[1]->kind);
		$this->assertSame(4, $actual[1]->pageNumber);
		$this->assertTrue($actual[1]->enabled);
		$this->assertFalse($actual[1]->current);

		$this->assertSame(PageShortcutKind::Normal, $actual[2]->kind);
		$this->assertSame(4, $actual[2]->pageNumber);
		$this->assertTrue($actual[2]->enabled);
		$this->assertFalse($actual[2]->current);

		$this->assertSame(PageShortcutKind::Normal, $actual[3]->kind);
		$this->assertSame(5, $actual[3]->pageNumber);
		$this->assertTrue($actual[3]->enabled);
		$this->assertTrue($actual[3]->current);

		$this->assertSame(PageShortcutKind::Normal, $actual[4]->kind);
		$this->assertSame(6, $actual[4]->pageNumber);
		$this->assertTrue($actual[4]->enabled);
		$this->assertFalse($actual[4]->current);

		$this->assertSame(PageShortcutKind::Short, $actual[5]->kind);
		$this->assertSame(6, $actual[5]->pageNumber);
		$this->assertTrue($actual[5]->enabled);
		$this->assertFalse($actual[5]->current);

		$this->assertSame(PageShortcutKind::Long, $actual[6]->kind);
		$this->assertSame(10, $actual[6]->pageNumber);
		$this->assertTrue($actual[6]->enabled);
		$this->assertFalse($actual[6]->current);
	}
}
