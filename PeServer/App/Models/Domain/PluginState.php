<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain;

abstract class PluginState
{
	/** 予約済み */
	public const RESERVED = 'reserved';
	/** 有効 */
	public const ENABLED = 'enabled';
	/** 無効 */
	public const DISABLED = 'disabled';
	/** これなぁ、どうしようかなぁ */
	public const CHECK_FAILED = 'check_failed';
}
