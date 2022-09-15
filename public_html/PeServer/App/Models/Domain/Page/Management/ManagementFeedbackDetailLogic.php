<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use Exception;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Dao\Entities\FeedbacksEntityDao;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Timer;

class ManagementFeedbackDetailLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		$this->validation('sequence', function ($key, $value) {
			$temp = $this->validator->isNotEmpty($key, $value);

			if($temp) {
				$database = $this->openDatabase();
				$feedbacksEntityDao = new FeedbacksEntityDao($database);

				$seq = (int)$value;
				$exists = $feedbacksEntityDao->selectExistsFeedback($seq);
				if(!$exists) {
					throw new Exception('404');
				}
			}
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$sequence = (int)$this->getRequest('sequence');

		$database = $this->openDatabase();
		$feedbacksEntityDao = new FeedbacksEntityDao($database);

		$detail = $feedbacksEntityDao->selectFeedbackDetail($sequence);

		$this->setValue('detail', $detail);
	}
}
