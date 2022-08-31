<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use PeServer\Core\DI\DiItem;

/**
 * 限定的DIコンテナ実装。
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
