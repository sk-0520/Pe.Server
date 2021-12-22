<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \PeServer\Core\ILogger;
use \PeServer\Core\ActionRequest;
use \PeServer\Core\ActionResponse;
use \PeServer\Core\HttpStatusCode;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\Core\Mvc\ValidationReceivable;
use \PeServer\Core\Mvc\Validations;
use \PeServer\Core\Throws\InvalidOperationException;
use \PeServer\Core\Throws\NotImplementedException;

/**
 * コントローラから呼び出されるロジック基底処理。
 */
abstract class LogicBase implements ValidationReceivable
{
	/**
	 * ロガー。
	 *
	 * @var ILogger
	 */
	protected $logger;
	/**
	 * リクエストデータ。
	 *
	 * @var ActionRequest
	 */
	private $_request;

	/**
	 * HTTPステータスコード。
	 *
	 * @var int
	 */
	private $_statusCode = HttpStatusCode::OK;
	/**
	 * 検証エラー。
	 *
	 * @var array<string,string[]>
	 */
	private $_errors = array();
	/**
	 * 応答データ。
	 *
	 * @var array<string,string|array>
	 */
	private $_values = array(); // @phpstan-ignore-line

	/**
	 * コントローラ内結果データ。
	 *
	 * @var array<string,string|array>
	 */
	public $result = array();

	/**
	 * Undocumented variable
	 *
	 * @var Validations
	 */
	protected $validation;

	/**
	 * 応答データ。
	 *
	 * @var ActionResponse|null
	 */
	private $_response = null;

	protected function __construct(LogicParameter $parameter)
	{
		$this->_request = $parameter->request;
		$this->logger = $parameter->logger;

		$this->logger->trace('LOGIC');

		if (is_null($this->validation)) { // @phpstan-ignore-line
			$this->validation = new Validations($this);
		}
	}

	/**
	 * リクエストデータの取得。
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	protected function getRequest(string $key, $default = null)
	{
		if (!$this->_request->exists($key)['exists']) {
			return $default;
		}
		return $this->_request->getValue($key);
	}

	/**
	 * 検証エラーが存在するか。
	 *
	 * @return boolean
	 */
	protected function hasError(): bool
	{
		return 0 < count($this->_errors);
	}

	protected function clearErrors(): void
	{
		$this->_errors = array();
	}

	protected function removeError(string $key): void
	{
		if (isset($this->_errors[$key])) {
			unset($this->_errors[$key]);
		}
	}

	protected function addError(string $key, string $message): void
	{
		if (isset($this->_errors[$key])) {
			$this->_errors[$key] = [$message];
		} else {
			$this->_errors[$key][] = $message;
		}
	}

	public function receiveError(string $key, int $kind, array $parameters): void
	{
		switch ($kind) {
			case Validations::KIND_EMPTY:
				$this->addError($key, var_export($parameters, true));
				break;

			case Validations::KIND_WHITE_SPACE:
				$this->addError($key, var_export($parameters, true));
				break;

			case Validations::KIND_LENGTH:
				$this->addError($key, var_export($parameters, true));
				break;

			default:
				throw new NotImplementedException(var_export($parameters)); // @phpstan-ignore-line
		}
	}

	/**
	 * 検証ロジック実装。
	 *
	 * @param LogicCallMode $callMode 呼び出し。
	 * @return void
	 */
	protected abstract function validateImpl(LogicCallMode $callMode): void;

	/**
	 * 実行ロジック実装。
	 *
	 * @param LogicCallMode $callMode 呼び出し。
	 * @return void
	 */
	protected abstract function executeImpl(LogicCallMode $callMode): void;

	/**
	 * 検証ロジック実装。
	 *
	 * @param LogicCallMode $callMode 呼び出し。
	 * @return void
	 */
	private function validate(LogicCallMode $callMode): void
	{
		$this->validateImpl($callMode);
	}

	/**
	 * 実行ロジック。
	 *
	 * @param LogicCallMode $callMode 呼び出し。
	 * @return void
	 */
	private function execute(LogicCallMode $callMode): void
	{
		$this->executeImpl($callMode);
	}

	/**
	 * ロジック処理。
	 *
	 * @param LogicCallMode $callMode 呼び出し。
	 * @return boolean
	 */
	public function run(LogicCallMode $callMode): bool
	{
		$this->validate($callMode);
		if ($this->hasError()) {
			return false;
		}

		$this->execute($callMode);

		if ($this->hasError()) {
			return false;
		}

		return true;
	}

	/**
	 * View表示用データの取得。
	 *
	 * @return array{status:int,errors:array<string,string[]>,values:array<string,string|array>}
	 */
	public function getViewData(): array // @phpstan-ignore-line
	{
		return [
			'status' => $this->_statusCode,
			'errors' => $this->_errors,
			'values' => $this->_values,
		];
	}

	/**
	 * 応答データ設定。
	 *
	 * @param ActionResponse $response
	 * @return void
	 */
	protected function setResponse(ActionResponse $response)
	{
		$this->_response = $response;
	}

	/**
	 * 応答データ取得。
	 *
	 * @return ActionResponse
	 * @throws InvalidOperationException 応答データ未設定
	 */
	public function getResponse(): ActionResponse
	{
		if (is_null($this->_response)) {
			throw new InvalidOperationException('not impl');
		}

		return $this->_response;
	}
}
