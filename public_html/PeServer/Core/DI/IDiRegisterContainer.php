<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use PeServer\Core\DI\DiItem;
use PeServer\Core\DI\IDiContainer;
use PeServer\Core\Text;

/**
 * 登録可能DIコンテナ。
 */
interface IDiRegisterContainer extends IDiContainer
{
	#region function

	/**
	 * 登録処理。
	 *
	 * @param string $id
	 * @phpstan-param class-string|non-empty-string $id
	 * @param DiItem $item
	 */
	public function add(string $id, DiItem $item): void;

	/**
	 * 登録アイテムの解除。
	 *
	 * @param string $id
	 * @phpstan-param class-string|non-empty-string $id
	 * @return DiItem|null 解除したアイテム。終了処理は呼び出し側で担保すること。存在しない場合は `null`。
	 */
	public function remove(string $id): ?DiItem;

	/**
	 * 簡易登録(クラス名指定)。
	 *
	 * 既に登録されている場合に既存アイテムは削除される。
	 *
	 * @param string $className
	 * @phpstan-param class-string $className
	 * @param int $lifecycle
	 * @phpstan-param DiItem::LIFECYCLE_* $lifecycle
	 */
	public function registerClass(string $className, int $lifecycle = DiItem::LIFECYCLE_TRANSIENT): void;

	/**
	 * 簡易登録(ID:クラス指定)。
	 *
	 * 既に登録されている場合に既存アイテムは削除される。
	 *
	 * @param string $id
	 * @phpstan-param class-string|non-empty-string $id
	 * @param string $className
	 * @phpstan-param class-string $className
	 * @param int $lifecycle
	 * @phpstan-param DiItem::LIFECYCLE_* $lifecycle
	 */
	public function registerMapping(string $id, string $className, int $lifecycle = DiItem::LIFECYCLE_TRANSIENT): void;

	/**
	 * 簡易登録(値指定)。
	 *
	 * 既に登録されている場合に既存アイテムは削除される。
	 *
	 * @param object|null $value
	 * @param string $id $valueの登録ID。未指定(空)の場合は $value の型名が使用される。
	 */
	public function registerValue(?object $value, string $id = Text::EMPTY): void;

	#endregion
}
