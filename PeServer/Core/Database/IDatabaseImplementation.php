<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

use PeServer\Core\Database\Management\IDatabaseManagement;

/**
 * DB実装処理。
 */
interface IDatabaseImplementation
{
	#region function

	/**
	 * `like` のエスケープ処理。
	 *
	 * @param string $value
	 * @return string
	 */
	public function escapeLike(string $value): string;

	/**
	 * バインド値のエスケープ処理。
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function escapeValue(mixed $value): string;

	/**
	 * DB実装管理処理の取得。
	 *
	 * @return IDatabaseManagement
	 */
	public function getManagement(): IDatabaseManagement;

	#endregion
}
