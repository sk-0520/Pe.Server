<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains\Page\Account;

use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\App\Models\Domains\Page\PageLogicBase;

class AccountUserPluginLogic extends PageLogicBase
{
	/**
	 * 新規作成か。
	 *
	 * 編集時は偽になる。
	 *
	 * @var boolean
	 */
	private bool $isRegister;

	public function __construct(LogicParameter $parameter, bool $isRegister)
	{
		parent::__construct($parameter);

		$this->isRegister = $isRegister;
	}

	protected function startup(LogicCallMode $callMode): void
	{
		if (!$this->isRegister) {
			//TODO: 編集時のあれこれ
		}

		$keys = [
			'account_plugin_display_name',
			'account_plugin_check_url',
			'account_plugin_lp_url',
			'account_plugin_project_url',
			'account_plugin_description',
		];

		if ($this->isRegister) {
			$keys = array_merge($keys, [
				'account_plugin_plugin_id',
				'account_plugin_plugin_name',
			]);
		}

		$this->registerParameterKeys($keys, true);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
	}
}
