<?php

declare(strict_types=1);

namespace PeServer\Core;

abstract class ConfigurationBase
{
	public abstract function get(): array;
}
