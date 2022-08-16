<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\DevelopmentApi;

use PeServer\Core\Mime;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Log\Logging;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\Core\Log\ILoggerFactory;
use PeServer\Core\Mvc\ILogicFactory;

define('NO_DEPLOY_START', '💩');
require_once 'deploy/php-deploy-receiver.php';
require_once 'deploy/script.php';

class DevelopmentApiInitializeLogic extends ApiLogicBase
{
	public function __construct(LogicParameter $parameter, private AppConfiguration $config, private ILoggerFactory $loggerFactory)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		// 結構なぐっだぐだ
		$scriptArgument = new class($this->config->rootDirectoryPath, $this->loggerFactory->create(\Deploy\ScriptArgument::class)) extends \Deploy\ScriptArgument // @phpstan-ignore-line
		{
			public function __construct(string $rootDirectoryPath, private ILogger $logger)
			{
				parent::__construct(  // @phpstan-ignore-line
					$rootDirectoryPath,
					'',
					'',
					[]
				);
			}
			public function log($message): void
			{
				$this->logger->info($message);
			}
		};

		$deployScript = new \DeployScript($scriptArgument); // @phpstan-ignore-line
		$deployScript->migrate($this->config->setting['persistence']);

		$this->setResponseJson(ResponseJson::success([
			'success' => true,
		]));
	}
}
