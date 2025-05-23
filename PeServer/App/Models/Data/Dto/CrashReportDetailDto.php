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

class CrashReportDetailDto extends DtoBase
{
	#region variable

	public int $sequence = -1;

	#[Mapping(converter: DateTimeConverter::class)]
	public DateTimeInterface $timestamp;

	#[Mapping(name: 'ip_address')]
	public string $ipAddress = Text::EMPTY;

	public string $version = Text::EMPTY;

	public string $revision = Text::EMPTY;

	public string $build = Text::EMPTY;

	#[Mapping(name: 'user_id')]
	public string $userId = Text::EMPTY;

	public string $exception = Text::EMPTY;
	public string $email = Text::EMPTY;
	public string $comment = Text::EMPTY;

	public string $report = Text::EMPTY;

	#[Mapping(name: 'developer_comment')]
	public string $developerComment = Text::EMPTY;

	#[Mapping(name: 'developer_status')]
	public ReportStatus $developerStatus;

	#endregion
}
