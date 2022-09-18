<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use Exception;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\Dao\Entities\CrashReportsEntityDao;
use PeServer\App\Models\Dao\Entities\FeedbacksEntityDao;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Archiver;
use PeServer\Core\Binary;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;
use PeServer\Core\Timer;

class ManagementCrashReportDetailLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppCryptography $appCryptography)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		$this->validation('sequence', function ($key, $value) {
			$temp = $this->validator->isNotEmpty($key, $value);

			if ($temp) {
				$database = $this->openDatabase();
				$crashReportsEntityDao = new CrashReportsEntityDao($database);

				$seq = (int)$value;
				$exists = $crashReportsEntityDao->selectExistsCrashReports($seq);
				if (!$exists) {
					throw new Exception('404');
				}
			}
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$sequence = (int)$this->getRequest('sequence');

		$database = $this->openDatabase();
		$crashReportsEntityDao = new CrashReportsEntityDao($database);

		$detail = $crashReportsEntityDao->selectCrashReportsDetail($sequence);

		$this->setValue('detail', $detail);
		if (Text::isNullOrWhiteSpace($detail->email)) {
			$this->setValue('email', Text::EMPTY);
		} else {
			$email = $this->appCryptography->decrypt($detail->email);
			$this->setValue('email', $email);
		}

		$compressReport = new Binary($detail->report);
		if ($compressReport->count()) {
			$report = Archiver::extractGzip($compressReport)->getRaw();
			$this->setValue('report', $report);
		} else {
			$this->setValue('report', Text::EMPTY);
		}
	}
}
