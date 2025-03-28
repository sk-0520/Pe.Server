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
	 * @phpstan-pure
	 */
	public function has(string $id): bool; //@phpstan-ignore-line [TYPE_INTERFACE]

	/**
	 * 指定したIDのオブジェクトを取得。
	 *
	 * @template T of object
	 * @param class-string|class-string<T>|non-empty-string $id
	 * @return object
	 * @phpstan-return ($id is class-string<T> ? T: object)
	 * @throws DiContainerException
	 */
	public function get(string $id): object; //@phpstan-ignore-line [TYPE_INTERFACE]

	/**
	 * クラス生成。
	 *
	 * @template T of object
	 * @param class-string|class-string<T>|non-empty-string $idOrClassName
	 * @param array<array-key,mixed> $arguments 生成パラメータ指定。
	 *  * int: 引数位置(0基点)。負数の場合で 0 に近い項目で割り当て可能(非`null`)なパラメータであれば順に消費されていく。
	 *  * string: 先頭が `$` で始まる場合は引数名、それ以外は型名と判断。型名の場合は一致するごとに消費されていく。
	 *  * 引数位置指定が優先される
	 *  * 未指定パラメータはDIコンテナ側で生成する
	 * @return object
	 * @phpstan-return ($idOrClassName is class-string<T> ? T: object)
	 * @throws DiContainerArgumentException パラメータ指定さている場合に対象ID($idOrClassName)がシングルトン・値の場合に投げられる。
	 */
	public function new(string $idOrClassName, array $arguments = []): object;

	/**
	 * コールバックを実施。
	 *
	 * @param callable $callback
	 * @param array<array-key,mixed> $arguments `new` を参照。
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
