<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Tool;

use Exception;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;

class ToolJsonLogic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'tool_json_input',
			'tool_json_kind',
			'tool_json_result',
			'has_result',
		], true);

		$this->setValue('has_result', false);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$this->validation('tool_json_input', function (string $key, string $value) {
			$this->validator->isNotEmpty($key, $value);
		});

		$this->validation('tool_json_kind', function (string $key, string $value) {
			$this->validator->isNotEmpty($key, $value);
			$this->validator->isMatch($key, '/format|minify/', $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			$this->setValue('tool_json_kind', 'encode');
			return;
		}

		$input = $this->getRequest('tool_json_input');
		$kind = $this->getRequest('tool_json_kind');
		$result = '';

		$json = null;
		try {
			$json = json_decode($input, true, 1024, JSON_THROW_ON_ERROR);
		} catch (Exception $ex) {
			$this->addError('tool_json_input', strval(($ex)));
			return;
		}

		switch ($kind) {
			case 'format':
				$result = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
				break;

			case 'minify':
				$result = json_encode($json);
				break;

			default:
				throw new Exception('kind');
		}

		$this->setValue('has_result', true);
		$this->setValue('tool_json_result', $result);
	}

	#endregion
}
