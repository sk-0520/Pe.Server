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

class FeedbackListItemDto extends DtoBase
{
	#region variable

	public int $sequence = -1;

	#[Mapping(converter: DateTimeConverter::class)]
	public DateTimeInterface $timestamp;
	#[Mapping(name: 'developer_status')]
	public ReportStatus $developerStatus = ReportStatus::None;
	public string $version = Text::EMPTY;
	public string $kind = Text::EMPTY;
	public string $subject = Text::EMPTY;

	#endregion
}
