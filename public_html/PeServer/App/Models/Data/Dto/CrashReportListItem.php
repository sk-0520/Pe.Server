<?php

declare(strict_types=1);

namespace PeServer\App\Models\Data\Dto;

use \DateTime;
use PeServer\Core\Serialization\Mapping;
use PeServer\Core\Text;

/**
 * @immutable
 */
class CrashReportListItem
{
	#region variable

	public int $sequence = -1;
	public string $timestamp = Text::EMPTY;
	public string $version = Text::EMPTY;

	#endregion
}
