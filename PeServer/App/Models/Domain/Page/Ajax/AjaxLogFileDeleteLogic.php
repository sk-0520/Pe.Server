<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Ajax;

use Exception;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;
use PeServer\Core\Throws\FileNotFoundException;

class AjaxLogFileDeleteLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppConfiguration $config)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		$logging = $this->config->setting->logging;
		/** @var string */
		$dirPath = $logging->loggers['file']->configuration['directory'];
		$logName = $this->getRequest('log_name');
		$logPath = Path::combine($dirPath, $logName);

		if (!File::exists($logPath)) {
			throw new FileNotFoundException();
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$logging = $this->config->setting->logging;
		/** @var string */
		$dirPath = $logging->loggers['file']->configuration['directory'];
		$logName = $this->getRequest('log_name');
		$logPath = Path::combine($dirPath, $logName);

		$result = [
			'path' => $logPath,
			'size' => File::getFileSize($logPath),
		];

		File::removeFile($logPath);

		$this->setResponseJson(ResponseJson::success($result));
	}

	#endregion
}
