<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Api\Development;

use Exception;
use PeServer\App\Models\AppConfiguration;
use PeServer\Core\ActionResponse;
use PeServer\Core\HttpStatusCode;
use \PeServer\Core\LogicBase;
use \PeServer\Core\LogicParameter;
use PeServer\Core\Mime;

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
		//NONE
	}

	protected function executeImpl(int $logicMode): void
	{
		//NONE
		$response = new ActionResponse(HttpStatusCode::OK, Mime::JSON, [
			'success' => true
		]);
		$this->setResponse($response);
	}
}
