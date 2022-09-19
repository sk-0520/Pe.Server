<?php

declare(strict_types=1);

namespace PeServer\App\Models\Data\Dto;

use \DateTime;
use PeServer\Core\Database\DtoBase;
use PeServer\Core\Text;

/**
 * @immutable
 */
class FeedbackListItemDto extends DtoBase
{
	#region variable

	public int $sequence = -1;
	public string $timestamp = Text::EMPTY;
	public string $version = Text::EMPTY;
	public string $kind = Text::EMPTY;
	public string $subject = Text::EMPTY;

	#endregion
}
