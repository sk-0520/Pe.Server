<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

use Override;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\ValuesBase;

/**
 * @extends ValuesBase<non-empty-string>
 */
readonly class MimeValues extends ValuesBase
{
	/**
	 * @param non-empty-string|list<non-empty-string> $values
	 */
	public function __construct(string|array $values)
	{
		parent::__construct($values);
	}

	#region ValuesBase

	/**
	 * @param non-empty-string $value
	 * @return non-empty-string
	 */
	#[Override]
	protected function convertValue(string|int|float|object $value): string
	{
		if (is_string($value)) {
			return $value;
		}

		throw new \InvalidArgumentException('Value must be a string.');
	}

	#[Override]
	protected function getSeparator(): string
	{
		return ',';
	}

	#endregion
}
