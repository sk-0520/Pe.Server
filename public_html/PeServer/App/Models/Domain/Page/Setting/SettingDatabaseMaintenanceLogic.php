<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Setting;

use \Throwable;
use PeServer\App\Models\AppConfiguration;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Database\IDatabaseContext;
use PeServer\Core\InitialValue;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Regex;
use PeServer\Core\StringUtility;

class SettingDatabaseMaintenanceLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'database_maintenance_statement',
			'executed',
			'result',
		], true);
		$this->setValue('executed', false);
		$this->setValue('result', null);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$this->validation('database_maintenance_statement', function (string $key, string $value) {
			$this->validator->isNotWhiteSpace($key, $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$statement = $this->getRequest('database_maintenance_statement');

		$database = $this->openDatabase();
		/** @var int|mixed[]|Throwable */
		$result = InitialValue::EMPTY_STRING;
		try {
			$database->transaction(function (IDatabaseContext $context) use (&$result, $statement) {
				/** @phpstan-var literal-string $statement */

				if (Regex::isMatch($statement, '/^\s*\bselect\b/')) { // select だけの判定はよくないけどしんどいのだ
					$result = $context->query($statement)->rows;
				} else {
					$result = $context->execute($statement)->resultCount;
				}
				return true;
			});
		} catch (Throwable $ex) {
			$result = $ex;
		}

		$this->setValue('executed', true);
		$this->setValue('result', $result);
	}
}
