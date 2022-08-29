<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use PeServer\Core\DI\DiItem;

/**
 * 限定的DIコンテナ。
 *
 * 生成元のデータを引き継ぎつつ生成元に影響を与えない。
 *
 * * 破棄処理は今回分のみ
 * * 未生成シングルトンは本処理で生成され、元コンテナでは生成されない
 *   * つまりは状態により元コンテナと差異が発生する可能性あり(ファクトリとかがその影響大)
 */
class ScopedDiContainer extends DiRegisterContainer implements IScopedDiContainer
{
	/**
	 * 生成。
	 *
	 * @param DiContainer $sourceContainer 元になるDIコンテナ。
	 */
	public function __construct(DiContainer $sourceContainer)
	{
		foreach ($sourceContainer->mapping as $key => $item) {
			if ($item->lifecycle === DiItem::LIFECYCLE_SINGLETON && !$item->hasSingletonValue()) {
				$this->mapping[$key] = new LocalScopeDiItem($item);
				continue;
			}
			$this->mapping[$key] = $item;
		}
	}

	#region IScopedDiContainer

	public function add(string $id, DiItem $item): void
	{
		parent::add($id, new LocalScopeDiItem($item));
	}

	protected function disposeImpl(): void
	{
		foreach ($this->mapping as $item) {
			if ($item instanceof LocalScopeDiItem) {
				$item->dispose();
			}
		}

		$this->mapping = [];
	}

	#endregion
}

/**
 * `ScopedDiContainer` で削除対象になるアイテム。
 */
final class LocalScopeDiItem extends DiItem
{
	public function __construct(DiItem $source)
	{
		parent::__construct($source->lifecycle, $source->type, $source->data);
	}
}
