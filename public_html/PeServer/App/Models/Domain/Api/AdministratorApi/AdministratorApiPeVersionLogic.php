<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Api\AdministratorApi;

use Exception;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\PeSettingEntityDao;
use PeServer\App\Models\Dao\Entities\PluginUrlsEntityDao;
use PeServer\App\Models\Domain\Api\ApiLogicBase;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\DefaultPlugin;
use PeServer\App\Models\Domain\PeVersionUpdater;
use PeServer\App\Models\Domain\PluginUrlKey;
use PeServer\App\Models\ResponseJson;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Text;

class AdministratorApiPeVersionLogic extends ApiLogicBase
{
	#region variable

	/**
	 * 要求JSON
	 *
	 * @var array<string,mixed>
	 */
	private array $requestJson;

	#endregion

	public function __construct(LogicParameter $parameter, private AppConfiguration $appConfig, private AppDatabaseCache $dbCache)
	{
		parent::__construct($parameter);

		$this->requestJson = $this->getRequestJson();
	}

	#region ApiLogicBase

	protected function validateImpl(LogicCallMode $callMode): void
	{
		$this->validateJsonProperty($this->requestJson, 'version', self::VALIDATE_JSON_PROPERTY_NON_EMPTY_TEXT);

		// 完全に実装ミス
		if ($this->hasError()) {
			throw new Exception();
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$database = $this->openDatabase();

		$version = $this->requestJson['version'];

		$result = $database->transaction(function (IDatabaseContext $context) use ($version) {
			$peVersionUpdater = new PeVersionUpdater();

			$peVersionUpdater->updateDatabase($context, $this->appConfig->setting->config->address->families->pluginUpdateInfoUrlBase, $version);

			return true;
		});
		if (!$result) {
			throw new Exception();
		}

		$this->dbCache->exportPluginInformation();

		$this->setResponseJson(ResponseJson::success([]));
	}

	#endregion
}
