<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Management;

use PeServer\App\Models\AuditLog;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\OutputBuffer;
use PeServer\Core\Text;
use Throwable;

class ManagementPhpEvaluateLogic extends PageLogicBase
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
			'result_is_string',
			'output',
			'output_is_string',
		], true);
		$this->setValue('executed', false);
		$this->setValue('execute_statement', null);
		$this->setValue('result', null);
		$this->setValue('result_is_string', false);
		$this->setValue('output', null);
		$this->setValue('output_is_string', false);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$this->validation('php_statement', function (string $key, string $value) {
			$this->validator->isNotWhiteSpace($key, $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$phpStatement = $this->getRequest('php_statement');
		$executeStatement = $phpStatement;

		$result = null;

		$this->setValue('executed', true);
		$this->setValue('execute_statement', $executeStatement);

		$output = Text::EMPTY;

		try {
			$output = OutputBuffer::get(function () use ($executeStatement, &$result) {
				$result = $this->evalStatement($executeStatement);
			});
			$this->setValue('output', $output);
			$this->setValue('output_is_string', !$output->hasNull());
		} catch (Throwable $ex) {
			$this->setValue('output', $ex);
			$output = (string)$ex;
		}
		$this->setValue('result', $result);
		$this->setValue('result_is_string', is_string($result));

		$this->writeAuditLogCurrentUser(AuditLog::ADMINISTRATOR_EXECUTE_PHP, [
			'php' => $executeStatement,
			'output' => Text::dump($output),
		]);
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
