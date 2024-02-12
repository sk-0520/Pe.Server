<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

abstract class PluginState
{
	public const ENABLED = 'enabled';
	public const DISABLED = 'disabled';
	public const CHECK_FAILED = 'check_failed';
}
