<?php

declare(strict_types=1);

namespace PeServer\App\Models\Data;

enum ReportStatus: string
{
	case None = 'none';
	case Working = 'working';
	case Completed ='completed';
	case Ignore ='ignore';
}
