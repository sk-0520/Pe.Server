<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use Exception;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\AppUrl;
use PeServer\App\Models\Dao\Domain\CrashReportDomainDao;
use PeServer\App\Models\Dao\Entities\CrashReportCommentsEntityDao;
use PeServer\App\Models\Dao\Entities\CrashReportsEntityDao;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Archive\Archiver;
use PeServer\Core\Binary;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Text;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Stopwatch;
use PeServer\Core\Store\SpecialStore;

class ManagementCrashReportDetailLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private SpecialStore $specialStore, private AppCryptography $appCryptography)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		$this->validation('sequence', function ($key, $value) {
			$hasSequence = $this->validator->isNotEmpty($key, $value);

			if ($hasSequence) {
				$database = $this->openDatabase();
				$crashReportsEntityDao = new CrashReportsEntityDao($database);

				$seq = (int)$value;
				$exists = $crashReportsEntityDao->selectExistsCrashReportsBySequence($seq);
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
		$crashReportDomainDao = new CrashReportDomainDao($database);

		$detail = $crashReportDomainDao->selectCrashReportsDetail($sequence);

		$this->setValue('detail', $detail);
		if (Text::isNullOrWhiteSpace($detail->email)) {
			$this->setValue('email', Text::EMPTY);
		} else {
			$email = $this->appCryptography->decrypt($detail->email);
			$this->setValue('email', $email);
		}

		$compressReport = new Binary($detail->report);
		if ($compressReport->count()) {
			$report = Archiver::extractGzip($compressReport)->toBase64();
			$this->setValue('report', $report);
		} else {
			$this->setValue('report', Text::EMPTY);
		}

		$this->setValue('developer_title', "[CR:$sequence] (edit title)");

		$exception = $detail->exception;

		$body = Text::replaceMap(
			<<<STR
{URL}

{VERSION}

```
{EXCEPTION}
```

---

(edit body)

STR,
			[
				'URL' => $this->specialStore->getRequestUrl()->toString(),
				'VERSION' => $detail->version,
				'EXCEPTION' => $exception
			]
		);
		$this->setValue('developer_body', $body);

		if ($callMode === LogicCallMode::Initialize) {
			$this->setValue('developer-comment', $detail->developerComment);
			return;
		}

		$developerComment = (string)$this->getRequest('developer-comment');

		$result = $database->transaction(function (IDatabaseContext $context) use ($sequence, $developerComment) {
			$crashReportCommentsEntityDao = new CrashReportCommentsEntityDao($context);

			if ($crashReportCommentsEntityDao->selectExistsCrashReportCommentsBySequence($sequence)) {
				$crashReportCommentsEntityDao->updateCrashReportComments($sequence, $developerComment);
			} else {
				$crashReportCommentsEntityDao->insertCrashReportComments($sequence, $developerComment);
			}

			return true;
		});
		if (!$result) {
			throw new HttpStatusException(HttpStatus::InternalServerError);
		}
	}
}
