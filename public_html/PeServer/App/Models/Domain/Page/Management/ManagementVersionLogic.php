<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AppCryptography;
use PeServer\App\Models\AppDatabaseCache;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Dao\Entities\PeSettingEntityDao;
use PeServer\App\Models\Dao\Entities\UserAuthenticationsEntityDao;
use PeServer\App\Models\Dao\Entities\UsersEntityDao;
use PeServer\App\Models\Domain\AccountValidator;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\App\Models\Domain\PeVersionUpdater;
use PeServer\App\Models\Domain\UserLevel;
use PeServer\App\Models\Domain\UserState;
use PeServer\App\Models\Domain\UserUtility;
use PeServer\App\Models\SessionKey;
use PeServer\Core\Collection\Arr;
use PeServer\Core\Cryptography;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Text;

class ManagementVersionLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter, private AppConfiguration $config, private AppDatabaseCache $dbCache)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'version',
		], true);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$this->validation('version', function ($key, $value) {
			$this->validator->isNotWhiteSpace($key, $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		$database = $this->openDatabase();

		if ($callMode === LogicCallMode::Initialize) {
			$peSettingEntityDao = new PeSettingEntityDao($database);
			$version = $peSettingEntityDao->selectPeSettingVersion();

			$this->setValue('version', $version);

			return;
		}

		$result = $database->transaction(function (IDatabaseContext $context) {
			$peVersionUpdater = new PeVersionUpdater();

			$peVersionUpdater->updateDatabase($context, $this->config->setting->config->address->families->pluginUpdateInfoUrlBase, $this->getRequest('version'));

			return true;
		});

		if (!$result) {
			$this->addCommonError('あかん');
			return;
		}

		$this->dbCache->exportPluginInformation();

		$this->addTemporaryMessage('バージョン更新');
	}
}
