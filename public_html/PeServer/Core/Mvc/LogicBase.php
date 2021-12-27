<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use LogicException;
use \PeServer\Core\ILogger;
use \PeServer\Core\ActionRequest;
use \PeServer\Core\ActionResponse;
use \PeServer\Core\ArrayUtility;
use \PeServer\Core\HttpStatus;
use PeServer\Core\I18n;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\Core\Mvc\ValidationReceivable;
use \PeServer\Core\Mvc\SessionNextState;
use \PeServer\Core\Mvc\Validations;
use \PeServer\Core\StringUtility;
use PeServer\Core\Throws\ArgumentException;
use \PeServer\Core\Throws\InvalidOperationException;
use \PeServer\Core\Throws\NotImplementedException;

/**
 * コントローラから呼び出されるロジック基底処理。
 */
abstract class LogicBase implements ValidationReceivable
{
	protected const SESSION_ALL_CLEAR = '';

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
	 */
	private HttpStatus $_httpStatus;
	/**
	 * 検証エラー。
	 *
	 * @var array<string,string[]>
	 */
	private $_errors = array();
	/**
	 * 応答データ。
	 *
	 * @var array<string,string|string[]|bool|int>
	 */
	private $_values = array();

	/**
	 * 要素設定がなされている場合に応答データのキーをこの項目に固定。
	 *
	 * @var string[]
	 */
	private $_keys = array();

	/**
	 * コントローラ内結果データ。
	 *
	 * @var array<string,string|array<mixed>>
	 */
	public $result = array();

	/**
	 * Undocumented variable
	 *
	 * @var Validator
	 */
	protected $validator;

	/**
	 * 応答データ。
	 *
	 * @var ActionResponse|null
	 */
	private $_response = null;

	private SessionStore $_session;
	private int $_sessionNextState = SessionNextState::NORMAL;

	protected function __construct(LogicParameter $parameter)
	{
		$this->_httpStatus = HttpStatus::ok();
		$this->_request = $parameter->request;
		$this->_session = $parameter->session;
		$this->logger = $parameter->logger;

		$this->logger->trace('LOGIC');

		$this->validator = new Validator($this);
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

	protected function getSession(string $key, mixed $default = null): mixed
	{
		return $this->_session->getOr($key, $default);
	}

	protected function setSession(string $key, mixed $value): void
	{
		$this->_session->set($key, $value);
	}
	protected function removeSession(string $key): void
	{
		$this->_session->remove($key);
	}
	protected function cancelSession(): void
	{
		$this->_sessionNextState = SessionNextState::CANCEL;
	}
	protected function restartSession(): void
	{
		$this->_sessionNextState = SessionNextState::RESTART;
	}
	protected function shutdownSession(): void
	{
		$this->_sessionNextState = SessionNextState::SHUTDOWN;
	}
	public function sessionNextState(): int
	{
		return $this->_sessionNextState;
	}

	/**
	 * Undocumented function
	 *
	 * @param string[] $keys
	 * @return void
	 */
	protected function registerParameterKeys(array $keys, bool $overwrite): void
	{
		$this->_keys = $keys;
		foreach ($this->_keys as $key) {
			if ($overwrite) {
				$value = $this->getRequest($key, '');
				$this->_values[$key] = $value;
			} else {
				$this->_values[$key] = '';
			}
		}
	}

	/**
	 * Undocumented function
	 *
	 * @param string $key
	 * @param string|string[]|bool|int $value
	 * @return void
	 */
	protected function setValue(string $key, $value): void
	{
		if (ArrayUtility::getCount($this->_keys)) {
			if (array_search($key, $this->_keys) === false) {
				throw new ArgumentException("key -> $key");
			}
		}

		$this->_values[$key] = $value;
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
			if (array_search($message, $this->_errors[$key]) === false) {
				$this->_errors[$key][] = $message;
			}
		} else {
			$this->_errors[$key] = [$message];
		}
	}

	public function receiveErrorMessage(string $key, string $message): void
	{
		$this->addError($key, $message);
	}
	public function receiveErrorKind(string $key, int $kind, array $parameters): void
	{
		$map = [
			Validator::KIND_EMPTY => I18n::ERROR_EMPTY,
			Validator::KIND_WHITE_SPACE => I18n::ERROR_WHITE_SPACE,
			Validator::KIND_LENGTH => I18n::ERROR_LENGTH,
			Validator::KIND_RANGE => I18n::ERROR_RANGE,
			Validator::KIND_MATCH => I18n::ERROR_MATCH,
			Validator::KIND_EMAIL => I18n::ERROR_EMAIL,
			Validator::KIND_WEBSITE => I18n::ERROR_WEBSITE,
		];

		$this->receiveErrorMessage($key, I18n::message($map[$kind], $parameters));
	}

	protected function validation(string $key, callable $callback): void
	{
		$value = $this->getRequest($key);
		$callback($key, $value);
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

	protected function startup(LogicCallMode $callMode): void
	{
		//NONE
	}

	protected function cleanup(LogicCallMode $callMode): void
	{
		//NONE
	}

	/**
	 * パラメータキー登録実装。
	 *
	 * @param LogicCallMode $callMode
	 * @return void
	 */
	private function registerKeys(LogicCallMode $callMode): void
	{
		//NONE
	}

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
		try {
			$this->startup($callMode);

			$this->registerKeys($callMode);

			$this->validate($callMode);
			if ($this->hasError()) {
				return false;
			}

			$this->execute($callMode);

			if ($this->hasError()) {
				return false;
			}

			return true;
		} finally {
			$this->cleanup($callMode);
		}
	}

	/**
	 * View表示用データの取得。
	 *
	 * @return TemplateParameter
	 */
	public function getViewData(): TemplateParameter
	{
		return new TemplateParameter(
			$this->_httpStatus,
			$this->_values,
			$this->_errors
		);
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
