<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domain\Page\Tool;

use Exception;
use PeServer\App\Models\Domain\Page\PageLogicBase;
use PeServer\Core\Mvc\Logic\LogicCallMode;
use PeServer\Core\Mvc\Logic\LogicParameter;

class ToolBase64Logic extends PageLogicBase
{
	public function __construct(LogicParameter $parameter)
	{
		parent::__construct($parameter);
	}

	#region PageLogicBase

	protected function startup(LogicCallMode $callMode): void
	{
		$this->registerParameterKeys([
			'tool_base64_input',
			'tool_base64_kind',
			'tool_base64_result',
			'has_result',
		], true);

		$this->setValue('has_result', false);
	}

	protected function validateImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			return;
		}

		$this->validation('tool_base64_input', function (string $key, string $value) {
			$this->validator->isNotEmpty($key, $value);
		});

		$this->validation('tool_base64_kind', function (string $key, string $value) {
			$this->validator->isNotEmpty($key, $value);
			$this->validator->isMatch($key, '/encode|decode/', $value);
		});
	}

	protected function executeImpl(LogicCallMode $callMode): void
	{
		if ($callMode === LogicCallMode::Initialize) {
			$this->setValue('tool_base64_kind', 'encode');
			return;
		}

		$input = $this->getRequest('tool_base64_input');
		$kind = $this->getRequest('tool_base64_kind');
		$result = '';

		switch ($kind) {
			case 'encode':
				$result = base64_encode($input);
				break;

			case 'decode':
				$result = base64_decode($input);
				break;

			default:
				throw new Exception('kind');
		}

		$this->setValue('has_result', true);
		$this->setValue('tool_base64_result', $result);
	}

	#endregion
}
