<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\ApplicationApi;

use Exception;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppMailer;
use PeServer\App\Models\AppTemplate;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\FeedbacksEntityDao;
use PeServer\App\Models\Dao\Entities\PeSettingEntityDao;
use PeServer\App\Models\Dao\Entities\SequenceEntityDao;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Mail\Attachment;
use PeServer\Core\Mail\EmailAddress;
use PeServer\Core\Mail\EmailMessage;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Web\Url;

class ApplicationApiVersionUpdateLogic extends ApiLogicBase
{
	#region variable

	public Url|null $redirectUrl = null;

	#endregion

	public function __construct(LogicParameter $parameter, private AppConfiguration $config)
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
		$database = $this->openDatabase();
		$peSettingEntityDao = new PeSettingEntityDao($database);

		$version = $peSettingEntityDao->selectPeSettingVersion();

		$this->redirectUrl = Url::parse(
			Text::replaceMap(
				$this->config->setting->config->address->families->peUpdateInfoUrlBase,
				[
					'VERSION' => $version,
					'UPDATE_INFO_NAME' => 'update.json',
				]
			)
		);
	}

	#endregion
}
