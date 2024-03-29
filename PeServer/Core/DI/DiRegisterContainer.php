<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use PeServer\Core\DI\DiContainer;
use PeServer\Core\DI\DiItem;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\ArgumentNullException;
use PeServer\Core\Throws\DiContainerRegisteredException;
use PeServer\Core\TypeUtility;

/**
 * 登録可能DIコンテナ実装。
 */
class DiRegisterContainer extends DiContainer implements IDiRegisterContainer
{
	#region IDiRegisterContainer

	public function add(string $id, DiItem $item): void
	{
		$this->throwIfDisposed();

		if ($this->has($id)) {
			throw new DiContainerRegisteredException($id);
		}

		if (Text::isNullOrWhiteSpace($id)) { //@phpstan-ignore-line [DOCTYPE]
			throw new ArgumentException('$id');
		}

		$this->mapping[$id] = $item;
	}

	public function remove(string $id): ?DiItem
	{
		$this->throwIfDisposed();

		if (!$this->has($id)) {
			return null;
		}

		$item = $this->mapping[$id];
		unset($this->mapping[$id]);

		return $item;
	}

	public function registerClass(string $className, int $lifecycle = DiItem::LIFECYCLE_TRANSIENT): void
	{
		$this->throwIfDisposed();

		$item = $this->remove($className);
		if ($item !== null) {
			$item->dispose();
		}

		$this->add($className, DiItem::class($className, $lifecycle));
	}

	public function registerMapping(string $id, string $className, int $lifecycle = DiItem::LIFECYCLE_TRANSIENT): void
	{
		$this->throwIfDisposed();

		$item = $this->remove($id);
		if ($item !== null) {
			$item->dispose();
		}

		$this->add($id, DiItem::class($className, $lifecycle));
	}

	public function registerValue(?object $value, string $id = Text::EMPTY): void
	{
		$this->throwIfDisposed();

		$registerId = $id;
		if (Text::isNullOrWhiteSpace($registerId)) {
			ArgumentNullException::throwIfNull($value, '$value');

			$registerId = TypeUtility::getType($value);
		}

		$item = $this->remove($registerId);
		if ($item !== null) {
			$item->dispose();
		}

		$this->add($registerId, DiItem::value($value));
	}

	#endregion
}
