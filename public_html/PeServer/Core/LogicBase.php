<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use \PeServer\Core\LogicParameter;

abstract class LogicBase
{
	protected $logger;
	private $request;

	private $errors = array();
	private $values = array();

	protected function __construct(LogicParameter $parameter)
	{
		$parameter->request;
		$this->logger = $parameter->logger;

		$this->logger->trace('LOGIC');
	}

	protected function getRequest(string $key, mixed $default = null): mixed
	{
		if (!$this->request->exists($key)) {
			return $default;
		}
		return $this->request->get($key);
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
