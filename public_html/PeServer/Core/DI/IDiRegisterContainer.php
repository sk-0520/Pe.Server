<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

/**
 * 登録可能DIコンテナ。
 */
interface IDiRegisterContainer
{
	/**
	 * 登録処理。
	 *
	 * @param string $id
	 * @phpstan-param class-string|non-empty-string $id
	 * @param DiItem $item
	 */
	function add(string $id, DiItem $item): void;

	/**
	 * 登録アイテムの解除。
	 *
	 * @param string $id
	 * @phpstan-param class-string|non-empty-string $id
	 * @return DiItem|null 解除したアイテム。終了処理は呼び出し側で担保すること。存在しない場合は `null`。
	 */
	function remove(string $id): ?DiItem;

	/**
	 * 簡易登録(クラス名指定)。
	 *
	 * @param string $className
	 * @phpstan-param class-string $className
	 * @param int $lifecycle
	 * @phpstan-param DiItem::LIFECYCLE_* $lifecycle
	 */
	function registerClass(string $className, int $lifecycle = DiItem::LIFECYCLE_TRANSIENT): void;

	/**
	 * 簡易登録(ID:クラス指定)。
	 *
	 * @param string $id
	 * @phpstan-param class-string|non-empty-string $id
	 * @param string $className
	 * @phpstan-param class-string $className
	 * @param int $lifecycle
	 * @phpstan-param DiItem::LIFECYCLE_* $lifecycle
	 */
	function registerMapping(string $id, string $className, int $lifecycle = DiItem::LIFECYCLE_TRANSIENT): void;
}
