<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Api\Development;

use PeServer\Core\Mime;
use \Deploy\ScriptArgument;
use PeServer\Core\ILogger;
use PeServer\Core\Log\Logging;
use PeServer\Core\Mvc\LogicBase;
use PeServer\Core\Mvc\ActionResponse;
use PeServer\Core\Http\HttpStatusCode;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Throws\CoreException;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domains\Api\ApiLogicBase;

define('NO_DEPLOY_START', '💩');
require_once 'deploy/php-deploy-receiver.php';
require_once 'deploy/script.php';

class DevelopmentInitializeLogic extends ApiLogicBase
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
		// 結構なぐっだぐだ
		$scriptArgument = new class() extends ScriptArgument // @phpstan-ignore-line
		{
			/**
			 * @var ILogger
			 */
			private $logger;
			public function __construct()
			{
				$this->logger = Logging::create('script');
				parent::__construct(  // @phpstan-ignore-line
					AppConfiguration::$rootDirectoryPath,
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
		$deployScript->migrate(AppConfiguration::$config['persistence']);


		$this->setContent(Mime::JSON, [
			'success' => true,
		]);
	}
}
