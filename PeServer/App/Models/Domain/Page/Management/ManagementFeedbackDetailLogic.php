<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use Exception;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Dao\Domain\FeedbackDomainDao;
use PeServer\App\Models\Dao\Entities\FeedbackCommentsEntityDao;
use PeServer\App\Models\Dao\Entities\FeedbacksEntityDao;
use PeServer\App\Models\Dao\Entities\FeedbackStatusEntityDao;
use PeServer\App\Models\Data\ReportStatus;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Stopwatch;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Text;

class ManagementFeedbackDetailLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private SpecialStore $specialStore)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		$this->validation('sequence', function ($key, $value) {
			$hasSequence = $this->validator->isNotEmpty($key, $value);

			if ($hasSequence) {
				$database = $this->openDatabase();
				$feedbacksEntityDao = new FeedbacksEntityDao($database);

				$seq = (int)$value;
				$exists = $feedbacksEntityDao->selectExistsFeedbacksBySequence($seq);
				if (!$exists) {
					throw new HttpStatusException(HttpStatus::NotFound);
				}
			} else {
				throw new HttpStatusException(HttpStatus::BadRequest);
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

		$this->setValue('report_status', ReportStatus::toArray());
		$this->setValue('detail', $detail);

		$title = $detail->subject;
		$this->setValue('developer_title', "[FB:$sequence] $title");

		$content = Text::join(PHP_EOL, Arr::map(Text::splitLines($detail->content), fn($a) => "> $a"));

		$body = Text::replaceMap(
			<<<STR
{URL}

Version: `{VERSION}`

{CONTENT}

---

(edit body)

STR,
			[
				'URL' => $this->specialStore->getRequestUrl()->toString(),
				'VERSION' => $detail->version,
				'CONTENT' => $content
			]
		);
		$this->setValue('developer_body', $body);

		if ($callMode === LogicCallMode::Initialize) {
			$this->setValue('developer-comment', $detail->developerComment);
			return;
		}

		$developerComment = (string)$this->getRequest('developer-comment');
		$developerStatus = ReportStatus::from($this->getRequest('developer-status'));

		$result = $database->transaction(function (IDatabaseContext $context) use ($sequence, $developerComment, $developerStatus) {
			$feedbackCommentsEntityDao = new FeedbackCommentsEntityDao($context);
			$feedbackStatusEntityDao = new FeedbackStatusEntityDao($context);

			if ($feedbackCommentsEntityDao->selectExistsFeedbackCommentsBySequence($sequence)) {
				$feedbackCommentsEntityDao->updateFeedbackComments($sequence, $developerComment);
			} else {
				$feedbackCommentsEntityDao->insertFeedbackComments($sequence, $developerComment);
			}

			$feedbackStatusEntityDao->upsertFeedbackStatus($sequence, $developerStatus);

			return true;
		});
		if (!$result) {
			throw new HttpStatusException(HttpStatus::InternalServerError);
		}
	}
}
