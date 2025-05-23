<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Dao\Domain\CrashReportDomainDao;
use PeServer\App\Models\Dao\Entities\CrashReportsEntityDao;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Mvc\Pagination;
use PeServer\Core\Stopwatch;
use PeServer\Core\TypeUtility;

class ManagementCrashReportListLogic extends PageLogicBase
{
	#region define

	public const ITEM_COUNT_IN_PAGE = 25;

	#endregion

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$pageNumber = Pagination::FIRST_PAGE_NUMBER;
		if ($callMode === LogicCallMode::Submit) {
			$requestPageNumber = $this->getRequest('page_number');
			if (TypeUtility::tryParsePositiveInteger($requestPageNumber, $temp)) {
				$pageNumber = $temp;
			}
		}

		$database = $this->openDatabase();
		$crashReportDomainDao = new CrashReportDomainDao($database);

		$totalCount = $crashReportDomainDao->selectCrashReportsPageTotalCount();

		$pagination = new Pagination($pageNumber, self::ITEM_COUNT_IN_PAGE, $totalCount);
		$items = $crashReportDomainDao->selectCrashReportsPageItems(($pagination->currentPageNumber - 1) * $pagination->itemCountInPage, $pagination->itemCountInPage);

		$this->setValue('items', $items);
		$this->setValue('pager', $pagination);
	}
}
