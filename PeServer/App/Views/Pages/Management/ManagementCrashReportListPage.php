<?php

declare(strict_types=1);

namespace PeServer\App\Views\Pages\Management;

use PeServer\Core\Mvc\Template\Node\RootProps;

/**
 * @template TRootProps of RootProps
 * @extends ManagementReportListPageBase<TRootProps>
 */
class ManagementCrashReportListPage extends ManagementReportListPageBase
{
	protected const bool IS_CRASH = true;
}
