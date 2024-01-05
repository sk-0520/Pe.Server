<?php

declare(strict_types=1);

namespace PeServer\App\Models\Data\Dto;

use DateTime;
use PeServer\Core\Database\DtoBase;
use PeServer\Core\Serialization\Mapping;
use PeServer\Core\Text;

class CrashReportDetailDto extends DtoBase
{
	#region variable

	public int $sequence = -1;

	public string $timestamp = Text::EMPTY;

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

	#endregion
}
