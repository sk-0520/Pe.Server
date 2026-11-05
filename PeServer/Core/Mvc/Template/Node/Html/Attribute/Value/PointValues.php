<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Html\Attribute\Value;

use Override;
use PeServer\Core\Mvc\Template\Node\Attribute\Value\ValuesBase;

use PeServer\Core\Image\Point;

/**
 * @extends ValuesBase<Point>
 */
readonly class PointValues extends ValuesBase
{
	/**
	 * @param Point|list<Point> $values
	 */
	public function __construct(Point|array $values)
	{
		parent::__construct($values);
	}

	#region ValuesBase

	/**
	 * @param Point $value
	 * @return string
	 */
	#[Override]
	protected function convertValue(string|int|float|object $value): string
	{
		if ($value instanceof Point) {
			return (string)$value->x . ',' . (string)$value->y;
		}

		throw new \InvalidArgumentException('Value must be an instance of Point.');
	}

	#[Override]
	protected function getSeparator(): string
	{
		return ',';
	}

	#endregion
}
