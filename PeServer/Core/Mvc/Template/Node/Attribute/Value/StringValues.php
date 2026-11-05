<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\Template\Node\Attribute\Value;

use PeServer\Core\Mvc\Template\Node\Attribute\Value\ValuesBase;

/**
 * @extends ValuesBase<string>
 */
readonly class StringValues extends ValuesBase
{
	/**
	 * 生成。
	 *
	 * @param string|list<string> $value
	 * @SuppressWarnings("php:S1185")
	 */
	public function __construct(string|array $value)
	{
		parent::__construct($value);
	}

	#region ValuesBase
	#endregion
}
