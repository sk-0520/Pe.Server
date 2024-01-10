<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use PeServer\Core\DI\IScopedDiContainer;
use PeServer\Core\Throws\DiContainerArgumentException;
use PeServer\Core\Throws\DiContainerException;
use Psr\Container\ContainerInterface;

/**
 * DIコンテナ。
 */
interface IDiContainer extends ContainerInterface
{
	#region ContainerInterface
	#endregion

	#region function

	/**
	 * 指定したIDが登録されているか。
	 *
	 * @param class-string|non-empty-string $id
	 * @return bool
	 */
	public function has(string $id): bool; //@phpstan-ignore-line [TYPE_INTERFACE]

	/**
	 * 指定したIDのオブジェクトを取得。
	 *
	 * @param class-string|non-empty-string $id
	 * @return mixed
	 * @throws DiContainerException
	 */
	public function get(string $id): mixed; //@phpstan-ignore-line [TYPE_INTERFACE]

	/**
	 * クラス生成。
	 *
	 * @param class-string|non-empty-string $idOrClassName
	 * @param array<int|string,mixed> $arguments 生成パラメータ指定。
	 *  * int: 引数位置(0基点)。負数の場合で 0 に近い項目で割り当て可能(非`null`)なパラメータであれば順に消費されていく。
	 *  * string: 先頭が `$` で始まる場合は引数名、それ以外は型名と判断。型名の場合は一致するごとに消費されていく。
	 *  * 引数位置指定が優先される
	 *  * 未指定パラメータはDIコンテナ側で生成する
	 * @return mixed
	 * @throws DiContainerArgumentException パラメータ指定さている場合に対象ID($idOrClassName)がシングルトン・値の場合に投げられる。
	 */
	public function new(string $idOrClassName, array $arguments = []): mixed;

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
	public function clone(): IScopedDiContainer;

	#endregion
}
