<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Home;

use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domains\Page\PageLogicBase;
use PeServer\Core\FileUtility;
use PeServer\Core\Mvc\Markdown;

class HomePrivacyLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$privacyPolicyPath = FileUtility::joinPath(AppConfiguration::$settingDirectoryPath, 'privacy_policy.md');
		$privacyPolicy = FileUtility::readContent($privacyPolicyPath);
		$this->setValue('privacy_policy', $privacyPolicy->getRaw());
	}
}
