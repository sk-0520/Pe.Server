<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Setting;

use PeServer\App\Models\AppConfiguration;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domains\Page\PageLogicBase;
use PeServer\Core\FileUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\FileNotFoundException;

class SettingLogDetailLogic extends PageLogicBase
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
		$logging = AppConfiguration::$config['logging'];
		$dirPath = (string)$logging['file']['directory'];
		//NONE
		$fileName = StringUtility::trim($this->getRequest('log_name'), '/\\.');
		$filePath = FileUtility::joinPath($dirPath, $fileName);
		if (!is_file($filePath)) {
			throw new FileNotFoundException();
		}

		$bytes = FileUtility::readContent($filePath);
		$logValue = $bytes->getRaw();

		$this->setValue('log_file', $filePath);
		$this->setValue('log_value', $logValue);
	}
}
