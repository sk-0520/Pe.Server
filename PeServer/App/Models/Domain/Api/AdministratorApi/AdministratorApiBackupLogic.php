<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\AdministratorApi;

use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Utc;

class AdministratorApiBackupLogic extends ApiLogicBase
{
	public function __construct(LogicParameter $parameter, private AppArchiver $appArchiver)
	{
		parent::__construct($parameter);
	}

	#region ApiLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$size = $this->appArchiver->backup();
		$this->appArchiver->rotate();

		$this->writeAuditLogCurrentUser(AuditLog::API_ADMINISTRATOR_BACKUP, ['size' => $size]);

		// 日曜だけバックアップ送信でいいわ
		$week = (int)$this->beginTimestamp->format('w');
		if ($week === 0) {
			$this->appArchiver->sendLatestArchive(AdministratorApiBackupLogic::class, true);
		}

		$this->setResponseJson(ResponseJson::success([
			'size' => $size,
		]));
	}

	#endregion
}
