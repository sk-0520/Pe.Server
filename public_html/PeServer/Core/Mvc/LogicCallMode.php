<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

/**
 * ロジック呼び出し。
 */
abstract class LogicCallMode
{
	/**
	 * 初期化。
	 */
	const INITIALIZE = 0;
	/**
	 * 確定。
	 */
	const SUBMIT = 1;
}
