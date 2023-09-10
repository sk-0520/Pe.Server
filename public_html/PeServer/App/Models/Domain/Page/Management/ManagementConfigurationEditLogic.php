<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use Exception;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\AppArchiver;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Binary;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\Environment;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Serialization\JsonSerializer;
use PeServer\Core\Text;

class ManagementConfigurationEditLogic extends PageLogicBase
{
	private string $settingPath = '';

	public function __construct(
		LogicParameter $parameter,
		private AppArchiver $appArchiver,
		private AppConfiguration $config
	) {
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$settingName = Path::setEnvironmentName('setting.json', Environment::get());
		$this->settingPath = Path::combine($this->config->settingDirectoryPath, $settingName);

		$this->setValue('env_name', Environment::get());
		$this->setValue('path', $this->settingPath);

		$this->registerParameterKeys([
			'json',
		], true);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Submit) {
			$jsonSerializer = new JsonSerializer();
			$json = $this->getRequest('json');
			try {
				$jsonSerializer->load(new Binary($json));
			} catch (Exception $ex) {
				$this->addError('json', $ex->getMessage());
			}
		}
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			$json = File::readContent($this->settingPath);
			$this->setValue('json', $json);
		} else {
			$jsonSerializer = new JsonSerializer();
			$json = $this->getRequest('json');
			$data = $jsonSerializer->load(new Binary($json));

			// 保存前にバックアップ
			$this->appArchiver->backup();

			File::writeJsonFile($this->settingPath, $data);

			$this->writeAuditLogCurrentUser(AuditLog::ADMINISTRATOR_SAVE_CONFIGURATION);
		}
	}
}
