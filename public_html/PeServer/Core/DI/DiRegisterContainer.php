<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use PeServer\Core\DefaultValue;
use PeServer\Core\DI\DiContainer;
use PeServer\Core\DI\DiItem;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\ArgumentNullException;
use PeServer\Core\Throws\DiContainerRegisteredException;
use PeServer\Core\TypeUtility;

class DiRegisterContainer extends DiContainer implements IDiRegisterContainer
{
	//[IDiRegisterContainer]

	public function add(string $id, DiItem $item): void
	{
		$this->throwIfDisposed();

		if ($this->has($id)) {
			throw new DiContainerRegisteredException($id);
		}

		if (Text::isNullOrWhiteSpace($id)) { //@phpstan-ignore-line
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
		if (!is_null($item)) {
			$item->dispose();
		}

		$this->add($className, DiItem::class($className, $lifecycle));
	}

	public function registerMapping(string $id, string $className, int $lifecycle = DiItem::LIFECYCLE_TRANSIENT): void
	{
		$this->throwIfDisposed();

		$item = $this->remove($id);
		if (!is_null($item)) {
			$item->dispose();
		}

		$this->add($id, DiItem::class($className, $lifecycle));
	}

	public function registerValue(?object $value, string $id = DefaultValue::EMPTY_STRING): void
	{
		$this->throwIfDisposed();

		$registerId = $id;
		if (Text::isNullOrWhiteSpace($registerId)) {
			if (is_null($value)) {
				throw new ArgumentNullException('$value');
			}
			$registerId = TypeUtility::getType($value);
		}

		/** @phpstan-var non-empty-string $registerId */
		$item = $this->remove($registerId);
		if (!is_null($item)) {
			$item->dispose();
		}

		$this->add($registerId, DiItem::value($value));
	}
}
