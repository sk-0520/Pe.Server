<?php

declare(strict_types=1);

namespace PeServer\App\Models\Data;

use PeServer\Core\I18n;

enum ReportStatus: string
{
	case None = 'none';
	case Working = 'working';
	case Ignore = 'ignore';
	case Completed = 'completed';

	/**
	 * @return ReportStatus[]
	 */
	public static function toArray(): array
	{
		return [
			ReportStatus::None,
			ReportStatus::Working,
			ReportStatus::Ignore,
			ReportStatus::Completed,
		];
	}

	public static function toString(self $reportStatus): string
	{
		return match ($reportStatus) {
			self::None => I18n::message('enum/report_status/none'),
			self::Working => I18n::message('enum/report_status/working'),
			self::Ignore => I18n::message('enum/report_status/ignore'),
			self::Completed => I18n::message('enum/report_status/completed'),
		};
	}
}
