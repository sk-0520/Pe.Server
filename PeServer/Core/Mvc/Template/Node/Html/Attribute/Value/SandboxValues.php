<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

use Override;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\ValuesBase;

use PeServer\Core\Image\Point;

/**
 * @extends ValuesBase<Sandbox>
 */
readonly class SandboxValues extends ValuesBase
{
	/**
	 * @param Sandbox|list<Sandbox> $values
	 */
	public function __construct(Sandbox|array $values)
	{
		parent::__construct($values);
	}

	#region ValuesBase

	/**
	 * @param Sandbox $value
	 * @return string
	 */
	#[Override]
	protected function convertValue(string|int|float|object $value): string
	{
		if ($value instanceof Sandbox) {
			return $value->value;
		}

		throw new \InvalidArgumentException('Value must be an instance of Sandbox.');
	}

	#[Override]
	protected function getSeparator(): string
	{
		return ' ';
	}

	#endregion
}
