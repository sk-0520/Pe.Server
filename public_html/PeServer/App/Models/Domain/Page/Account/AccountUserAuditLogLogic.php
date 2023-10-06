<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Account;

use PeServer\App\Models\Dao\Entities\UserAuditLogsEntityDao;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Pagination;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\TypeUtility;

class AccountUserAuditLogLogic extends PageLogicBase
{
	#region define

	public const ITEM_COUNT_IN_PAGE = 10;

	#endregion

	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$userInfo = $this->getAuditUserInfo();
		if ($userInfo === null) {
			throw new InvalidOperationException();
		}
		$userId = $userInfo->getUserId();

		$pageNumber = Pagination::FIRST_PAGE_NUMBER;
		if ($callMode === LogicCallMode::Submit) {
			$requestPageNumber = $this->getRequest('page_number');
			TypeUtility::tryParseInteger($requestPageNumber, $pageNumber);
		}

		$database = $this->openDatabase();
		$userAuditLogsEntityDao = new UserAuditLogsEntityDao($database);

		$totalCount = $userAuditLogsEntityDao->selectAuditLogsPageTotalCountFromUserId($userId);
		$pagination = new Pagination($pageNumber, self::ITEM_COUNT_IN_PAGE, $totalCount);
		$items = $userAuditLogsEntityDao->selectAuditLogsPageItemsFromUserId($userId, ($pagination->currentPageNumber - 1) * $pagination->itemCountInPage, $pagination->itemCountInPage);

		$this->setValue('items', $items);
		$this->setValue('pager', $pagination);
	}

	#endregion
}
