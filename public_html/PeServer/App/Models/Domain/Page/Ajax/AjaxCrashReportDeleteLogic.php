<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Ajax;

use PeServer\App\Models\Dao\Entities\CrashReportCommentsEntityDao;
use PeServer\App\Models\Dao\Entities\CrashReportsEntityDao;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Throws\FileNotFoundException;

class AjaxCrashReportDeleteLogic extends PageLogicBase
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
		$database->transaction(function(IDatabaseContext $context) use($sequence) {
			$crashReportsEntityDao = new CrashReportsEntityDao($context);
			$crashReportCommentsEntityDao = new CrashReportCommentsEntityDao($context);

			$crashReportCommentsEntityDao->deleteCrashReportCommentsBySequence($sequence);
			$crashReportsEntityDao->deleteCrashReportsBySequence($sequence);
			return true;
		});

		$this->setResponseJson(ResponseJson::success([]));
	}

	#endregion
}
