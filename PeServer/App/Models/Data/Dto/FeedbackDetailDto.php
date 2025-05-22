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

class FeedbackDetailDto extends DtoBase
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

	#[Mapping(name: 'first_execute_timestamp')]
	public string $firstExecuteTimestamp = Text::EMPTY;
	#[Mapping(name: 'first_execute_version')]
	public string $firstExecuteVersion = Text::EMPTY;

	public string $process = Text::EMPTY;
	public string $platform = Text::EMPTY;
	public string $os = Text::EMPTY;
	public string $clr = Text::EMPTY;

	public string $kind = Text::EMPTY;
	public string $subject = Text::EMPTY;
	public string $content = Text::EMPTY;

	#[Mapping(name: 'developer_comment')]
	public string $developerComment = Text::EMPTY;

	#[Mapping(name: 'developer_status')]
	public ReportStatus $developerStatus;


	#endregion
}
