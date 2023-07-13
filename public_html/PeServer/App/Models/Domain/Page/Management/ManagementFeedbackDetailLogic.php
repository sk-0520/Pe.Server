<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use Exception;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Dao\Domain\FeedbackDomainDao;
use PeServer\App\Models\Dao\Entities\FeedbackCommentsEntityDao;
use PeServer\App\Models\Dao\Entities\FeedbacksEntityDao;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Throws\HttpStatusException;
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
			$hasSequence = $this->validator->isNotEmpty($key, $value);

			if($hasSequence) {
				$database = $this->openDatabase();
				$feedbacksEntityDao = new FeedbacksEntityDao($database);

				$seq = (int)$value;
				$exists = $feedbacksEntityDao->selectExistsFeedbacksBySequence($seq);
				if(!$exists) {
					//TODO: HttpStatusException
					throw new Exception('404');
				}
			} else {
				throw new HttpStatusException(HttpStatus::badRequest());
			}
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$sequence = (int)$this->getRequest('sequence');
		$this->result['sequence'] = $sequence;

		$database = $this->openDatabase();
		$feedbackDomainDao = new FeedbackDomainDao($database);

		$detail = $feedbackDomainDao->selectFeedbackDetailBySequence($sequence);

		$this->setValue('detail', $detail);

		if ($callMode === LogicCallMode::Initialize) {
			$this->setValue('developer-comment', $detail->developerComment);
			return;
		}

		$developerComment = (string)$this->getRequest('developer-comment');

		$result = $database->transaction(function (IDatabaseContext $context) use ($sequence, $developerComment) {
			$feedbackCommentsEntityDao = new FeedbackCommentsEntityDao($context);

			if ($feedbackCommentsEntityDao->selectExistsFeedbackCommentsBySequence($sequence)) {
				$feedbackCommentsEntityDao->updateFeedbackComments($sequence, $developerComment);
			} else {
				$feedbackCommentsEntityDao->insertFeedbackComments($sequence, $developerComment);
			}

			return true;
		});
		if (!$result) {
			throw new HttpStatusException(HttpStatus::internalServerError());
		}
	}
}
