<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Ajax;

use PeServer\App\Models\Dao\Entities\FeedbackCommentsEntityDao;
use PeServer\App\Models\Dao\Entities\FeedbacksEntityDao;
use PeServer\App\Models\Dao\Entities\FeedbackStatusEntityDao;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Throws\FileNotFoundException;

class AjaxFeedbackDeleteLogic extends PageLogicBase
{
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
		$sequence = (int)$this->getRequest('sequence');

		$database = $this->openDatabase();
		$database->transaction(function (IDatabaseContext $context) use ($sequence) {
			$feedbacksEntityDao = new FeedbacksEntityDao($context);
			$feedbackCommentsEntityDao = new FeedbackCommentsEntityDao($context);
			$feedbackStatusEntityDao = new FeedbackStatusEntityDao($context);

			$feedbackCommentsEntityDao->deleteFeedbackCommentsBySequence($sequence);
			$feedbackStatusEntityDao->deleteFeedbackStatusBySequence($sequence);
			$feedbacksEntityDao->deleteFeedbacksBySequence($sequence);
			return true;
		});

		$this->setResponseJson(ResponseJson::success([]));
	}

	#endregion
}
