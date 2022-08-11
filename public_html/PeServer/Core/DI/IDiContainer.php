<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use PeServer\Core\Throws\DiContainerException;

/**
 * DIコンテナ。
 */
interface IDiContainer
{
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
}
