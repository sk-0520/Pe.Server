<?php

declare(strict_types=1);

namespace PeServer\App\Models\Domains;

use \Exception;
use \PeServer\App\Models\Domains\LogicParameter;

abstract class LogicBase
{
	protected $logger;

	protected $inputs = array();
	protected $errors = array();
	protected $values = array();

	protected function __construct(LogicParameter $parameter)
	{
		$this->logger = $parameter->logger;

		$this->logger->trace('LOGIC');
	}

	public function hasError()
	{
		return count($this->errors);
	}

	protected abstract function validateImpl(int $logicMode): void;
	protected abstract function executeImpl(int $logicMode): void;

	private function validate(int $logicMode)
	{
		$this->validateImpl($logicMode);
	}

	private function execute(int $logicMode)
	{
		$this->executeImpl($logicMode);
	}

	public function run(int $logicMode): bool
	{
		$this->validate($logicMode);
		if ($this->hasError()) {
			return false;
		}

		$this->execute($logicMode);
		if ($this->hasError()) {
			return false;
		}

		return true;
	}

	public function getData(): array
	{
		return [
			'errors' => $this->errors,
			'values' => $this->values,
		];
	}
}
