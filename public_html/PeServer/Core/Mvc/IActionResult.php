<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

/**
 * アクションメソッドの結果操作。
 */
interface IActionResult
{
	/**
	 *  * アクションメソッドの結果操作を実行。
	 *
	 * @return void
	 */
	public function execute(): void;
}
