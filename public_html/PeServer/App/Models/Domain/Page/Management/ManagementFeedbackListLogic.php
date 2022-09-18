<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Dao\Entities\FeedbacksEntityDao;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Pagination;
use PeServer\Core\Timer;
use PeServer\Core\TypeUtility;

class ManagementFeedbackListLogic extends PageLogicBase
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
		$pageNumber = 1;
		if ($callMode->isSubmit()) {
			$requestPageNumber = $this->getRequest('page_number');
			TypeUtility::tryParseInteger($requestPageNumber, $pageNumber);
		}

		$database = $this->openDatabase();
		$feedbacksEntityDao = new FeedbacksEntityDao($database);

		$totalCount = $feedbacksEntityDao->selectFeedbacksPageTotalCount();

		$pagination = new Pagination($pageNumber, self::ITEM_COUNT_IN_PAGE, $totalCount);
		$items = $feedbacksEntityDao->selectFeedbacksPageItems(($pagination->currentPageNumber - 1) * $pagination->itemCountInPage, $pagination->itemCountInPage);

		$this->setValue('total_count', $totalCount);
		$this->setValue('items', $items);
		$this->setValue('pager', $pagination);
	}
}
