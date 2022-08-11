<?php

declare(strict_types=1);

namespace PeServer\Core\DI;

use PeServer\Core\DI\DiItem;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\DiContainerRegisteredException;

class DiRegisterContainer extends DiContainer implements IDiRegisterContainer
{
	//[IDiRegisterContainer]

	public function add(string $id, DiItem $item): void
	{
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
		if (!$this->has($id)) {
			return null;
		}

		$item = $this->mapping[$id];
		unset($this->mapping[$id]);

		return $item;
	}

	public function registerClass(string $className, int $lifecycle = DiItem::LIFECYCLE_TRANSIENT): void
	{
		$this->add($className, DiItem::class($className, $lifecycle));
	}

	public function registerMapping(string $id, string $className, int $lifecycle = DiItem::LIFECYCLE_TRANSIENT): void
	{
		$this->add($id, DiItem::class($className, $lifecycle));
	}
}
