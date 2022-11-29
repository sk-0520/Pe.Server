<?php

declare(strict_types=1);

namespace PeServer\Core\Database;

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
	function escapeLike(string $value): string;

	/**
	 * バインド値のエスケープ処理。
	 *
	 * @param mixed $value
	 * @return string
	 */
	function escapeValue(mixed $value): string;

	#endregion
}
