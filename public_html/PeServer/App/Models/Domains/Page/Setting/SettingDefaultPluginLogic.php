<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Setting;

use PeServer\App\Models\AppCryptography;
use PeServer\Core\Uuid;
use PeServer\Core\I18n;
use PeServer\Core\Database;
use PeServer\Core\StringUtility;
use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domains\UserLevel;
use PeServer\App\Models\Domains\UserState;
use PeServer\Core\Mvc\Validations;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domains\AccountValidator;
use PeServer\App\Models\Domains\Page\PageLogicBase;
use PeServer\App\Models\Database\Entities\UsersEntityDao;
use PeServer\App\Models\Database\Entities\UserAuthenticationsEntityDao;
use PeServer\Core\Cryptography;
use PeServer\Core\Throws\InvalidOperationException;

class SettingDefaultPluginLogic extends PageLogicBase
{
	/** @var array{plugin_id:string,plugin_name:string,check_url:string,project_url:string}[] */
	private static $defaultPlugins = [
		[
			'plugin_id' => '4524fc23-ebb9-4c79-a26b-8f472c05095e',
			'plugin_name' => 'Pe.Plugins.DefaultTheme',
			'check_url' => '',
			'project_url' => 'https://bitbucket.org/sk_0520/pe'
		],
		[
			'plugin_id' => '67f0fa7d-52d3-4889-b595-be3703b224eb',
			'plugin_name' => 'Pe.Plugins.Reference.ClassicTheme',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.ClassicTheme.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe'
		],
		[
			'plugin_id' => '2e5c72c5-270f-4b05-afb9-c87f3966ecc5',
			'plugin_name' => 'Pe.Plugins.Reference.Clock',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.Clock.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe'
		],
		[
			'plugin_id' => '799ce8bd-8f49-4e8f-9e47-4d4873084081',
			'plugin_name' => 'Pe.Plugins.Reference.Eyes',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.Eyes.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe'
		],
		[
			'plugin_id' => '9dcf441d-9f8e-494f-89c1-814678bbc42c',
			'plugin_name' => 'Pe.Plugins.Reference.FileFinder',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.FileFinder.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe'
		],
		[
			'plugin_id' => '4fa1a634-6b32-4762-8ae8-3e1cf6df9db1',
			'plugin_name' => 'Pe.Plugins.Reference.Html',
			'check_url' => 'https://bitbucket.org/sk_0520/pe/downloads/update-Pe.Plugins.Reference.Html.json',
			'project_url' => 'https://bitbucket.org/sk_0520/pe'
		],
	];

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
		if ($callMode->isInitialize()) {
			$this->setValue('plugins', self::$defaultPlugins);
			return;
		}
	}
}
