<?php

declare(strict_types=1);

namespace PeServer\Core\Schema;

use PeServer\Core\Schema\SchemaBase;

class IntSchema extends SchemaBase
{
	#region variable

	private int|null $min = null;
	private int|null $max = null;

	#endregion

	#region function

	public function min(int $min): self
	{
		$this->min = $min;
		return $this;
	}

	public function max(int $max): self
	{
		$this->max = $max;
		return $this;
	}

	#endregion
}
