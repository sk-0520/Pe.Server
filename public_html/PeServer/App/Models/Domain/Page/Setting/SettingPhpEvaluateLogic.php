<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Setting;

use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\OutputBuffer;
use Throwable;

class SettingPhpEvaluateLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'php_statement',
			'executed',
			'execute_statement',
			'result',
			'output',
		], true);
		$this->setValue('executed', false);
		$this->setValue('execute_statement', null);
		$this->setValue('result', null);
		$this->setValue('output', null);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$this->validation('php_statement', function (string $key, string $value) {
			$this->validator->isNotWhiteSpace($key, $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode->isInitialize()) {
			return;
		}

		$phpStatement = $this->getRequest('php_statement');
		$executeStatement = $phpStatement;

		$result = null;

		$this->setValue('executed', true);
		$this->setValue('execute_statement', $executeStatement);

		try {
			$output = OutputBuffer::get(function () use ($executeStatement, &$result) {
				$result = $this->evalStatement($executeStatement);
			});
			$this->setValue('output', $output);
		} catch (Throwable $ex) {
			$this->setValue('output', $ex);
		}
		$this->setValue('result', $result);
	}

	/**
	 * PHP文を実行。
	 *
	 * @param string $statement
	 * @return mixed
	 * @SuppressWarnings(PHPMD.EvalExpression)
	 */
	private function evalStatement(string $statement): mixed
	{
		return eval($statement);
	}
}
