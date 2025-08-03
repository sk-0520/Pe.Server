<?php

declare(strict_types=1);

namespace PeServer\App\Models\Data\Dto;

use DateTime;
use DateTimeInterface;
use PeServer\App\Models\Data\ReportStatus;
use PeServer\Core\Database\DtoBase;
use PeServer\Core\Serialization\Converter\DateTimeConverter;
use PeServer\Core\Serialization\Mapping;
use PeServer\Core\Text;
use PeServer\Core\Utc;

class CrashReportListItemDto extends DtoBase
{
	#region variable

	public int $sequence = -1;
	#[Mapping(converter: DateTimeConverter::class)]
	public DateTimeInterface $timestamp;
	public string $version = Text::EMPTY;
	#[Mapping(name: 'exception_subject')]
	public string $exceptionSubject = Text::EMPTY;
	#[Mapping(name: 'developer_status')]
	public ReportStatus $developerStatus = ReportStatus::None;

	#endregion
}
