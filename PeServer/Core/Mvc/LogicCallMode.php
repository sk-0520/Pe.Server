<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

/**
 * ロジック呼び出し。
 */
enum LogicCallMode
{
	#region define

	/**
	 * 初期化状態生値。
	 */
	case Initialize;
	/**
	 * 確定状態生値。
	 */
	case Submit;

	#endregion
}
