<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Api\Development;

use \Exception;
use \PeServer\Core\ActionResponse;
use \PeServer\Core\HttpStatusCode;
use \PeServer\Core\LogicBase;
use \PeServer\Core\LogicParameter;
use \PeServer\Core\Mime;
use \PeServer\App\Models\AppConfiguration;
use \Deploy\ScriptArgument;
use PeServer\Core\Log\Logging;

define('NO_DEPLOY_START', '💩');
require_once 'deploy/php-deploy-receiver.php';
require_once 'deploy/script.php';

class DevelopmentInitializeLogic extends LogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function validateImpl(int $logicMode): void
	{
		if (AppConfiguration::isProductionEnvironment()) {
			throw new Exception('dev or test only');
		}
	}

	protected function executeImpl(int $logicMode): void
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

		$deployScript = new \DeployScript($scriptArgument);  // @phpstan-ignore-line
		$deployScript->migrate(AppConfiguration::$json['persistence']);


		$response = ActionResponse::json([
			'success' => true
		]);
		$this->setResponse($response);
	}
}
