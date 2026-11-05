<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Attribute\Value;

interface IAttributeValue
{
	#region function

	public function toValue(): string;

	#endregion
}
