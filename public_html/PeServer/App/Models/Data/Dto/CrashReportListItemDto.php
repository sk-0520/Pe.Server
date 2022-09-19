<?php

declare(strict_types=1);

namespace PeServer\App\Models\Data\Dto;

use \DateTime;
use PeServer\Core\Database\DtoBase;
use PeServer\Core\Serialization\Mapping;
use PeServer\Core\Text;

/**
 * @immutable
 */
class CrashReportListItemDto extends DtoBase
{
	#region variable

	public int $sequence = -1;
	public string $timestamp = Text::EMPTY;
	public string $version = Text::EMPTY;
	#[Mapping(name: 'exception_subject')]
	public string $exceptionSubject = Text::EMPTY;

	#endregion
}
