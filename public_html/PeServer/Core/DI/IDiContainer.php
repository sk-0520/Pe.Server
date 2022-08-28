<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use PeServer\Core\DI\IScopedDiContainer;
use PeServer\Core\Throws\DiContainerArgumentException;
use PeServer\Core\Throws\DiContainerException;

/**
 * DIコンテナ。
 */
interface IDiContainer
{
	#region function

	/**
	 * 指定したIDが登録されているか。
	 *
	 * @param string $id
	 * @phpstan-param class-string|non-empty-string $id
	 * @return bool
	 */
	function has(string $id): bool;

	/**
	 * 指定したIDのオブジェクトを取得。
	 *
	 * @param string $id
	 * @phpstan-param class-string|non-empty-string $id
	 * @return mixed
	 * @throws DiContainerException
	 */
	function get(string $id): mixed;

	/**
	 * クラス生成。
	 *
	 * @param string $idOrClassName
	 * @phpstan-param class-string|non-empty-string $idOrClassName
	 * @param array<int|string,mixed> $arguments 生成パラメータ指定。
	 *  * int: 引数位置(0基点)。負数の場合で 0 に近い項目で割り当て可能(非`null`)なパラメータであれば順に消費されていく。
	 *  * string: 先頭が `$` で始まる場合は引数名、それ以外は型名と判断。型名の場合は一致するごとに消費されていく。
	 *  * 引数位置指定が優先される
	 *  * 未指定パラメータはDIコンテナ側で生成する
	 * @return mixed
	 * @throws DiContainerArgumentException パラメータ指定さている場合に対象ID($idOrClassName)がシングルトン・値の場合に投げられる。
	 */
	function new(string $idOrClassName, array $arguments = []): mixed;

	/**
	 * コールバックを実施。
	 *
	 * @param callable $callback
	 * @param array<int|string,mixed> $arguments `new` を参照。
	 * @return mixed
	 */
	public function call(callable $callback, array $arguments = []): mixed;

	/**
	 * 現在のDIコンテナを複製。
	 *
	 * @return IScopedDiContainer
	 */
	function clone(): IScopedDiContainer;

	#endregion
}
