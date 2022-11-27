<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Dao\Entities\CrashReportsEntityDao;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Pagination;
use PeServer\Core\Timer;
use PeServer\Core\TypeUtility;

class ManagementCrashReportListLogic extends PageLogicBase
{
	#region define

	const ITEM_COUNT_IN_PAGE = 25;

	#endregion

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$pageNumber = Pagination::FIRST_PAGE_NUMBER;
		if ($callMode === LogicCallMode::Submit) {
			$requestPageNumber = $this->getRequest('page_number');
			TypeUtility::tryParseInteger($requestPageNumber, $pageNumber);
		}

		$database = $this->openDatabase();
		$feedbacksEntityDao = new CrashReportsEntityDao($database);

		$totalCount = $feedbacksEntityDao->selectCrashReportsPageTotalCount();

		$pagination = new Pagination($pageNumber, self::ITEM_COUNT_IN_PAGE, $totalCount);
		$items = $feedbacksEntityDao->selectCrashReportsPageItems(($pagination->currentPageNumber - 1) * $pagination->itemCountInPage, $pagination->itemCountInPage);

		$this->setValue('items', $items);
		$this->setValue('pager', $pagination);
	}
}
