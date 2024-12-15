<?php

declare(strict_types=1);

namespace PeServer\Core;

use Attribute;
use Stringable;
use PeServer\Core\Code;
use PeServer\Core\Collection\Arr;
use PeServer\Core\InitializeChecker;
use PeServer\Core\Text;

#[Attribute(Attribute::TARGET_PROPERTY)]
class I18nProperty implements Stringable
{
	//I18n::message('enum/user_level/user'),
	public function __construct(
		public string $key = Text::EMPTY,
		public string $className = Text::EMPTY
	) {
	}

	#region function

	public function toString(): string
	{
		return "";
	}

	#endregion

	#region Stringable

	public function __toString(): string
	{
		return $this->toString();
	}

	#endregion

}
