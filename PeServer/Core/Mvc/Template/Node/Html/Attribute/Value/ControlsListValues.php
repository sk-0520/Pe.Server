<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

use Override;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\ValuesBase;

use PeServer\Core\Mvc\Template\Node\Html\Attribute\Value\ControlsList;

/**
 * @extends ValuesBase<ControlsList>
 */
readonly class ControlsListValues extends ValuesBase
{
	/**
	 * @param ControlsList|list<ControlsList> $values
	 */
	public function __construct(ControlsList|array $values)
	{
		parent::__construct($values);
	}

	#region ValuesBase

	/**
	 * @param ControlsList $value
	 * @return string
	 */
	#[Override]
	protected function convertValue(string|int|float|object $value): string
	{
		if ($value instanceof ControlsList) {
			return $value->value;
		}

		throw new \InvalidArgumentException('Value must be an instance of ControlsList.');
	}

	#endregion
}
