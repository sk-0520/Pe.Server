<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \DateInterval;
use PeServer\Core\I18n;
use PeServer\Core\Bytes;
use PeServer\Core\ILogger;
use PeServer\Core\ArrayUtility;
use PeServer\Core\FileUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\Validations;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Mvc\ActionResponse;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Mvc\SessionNextState;
use PeServer\Core\Store\TemporaryStore;
use PeServer\Core\Mvc\IValidationReceiver;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * コントローラから呼び出されるロジック基底処理。
 */
abstract class LogicBase implements IValidationReceiver
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
	 * @var HttpRequest
	 */
	private $request;

	/**
	 * HTTPステータスコード。
	 */
	private HttpStatus $httpStatus;
	/**
	 * 検証エラー。
	 *
	 * @var array<string,string[]>
	 */
	private $errors = array();
	/**
	 * 応答データ。
	 *
	 * @var array<string,mixed>
	 */
	private $values = array();

	/**
	 * 要素設定がなされている場合に応答データのキーをこの項目に固定。
	 *
	 * @var string[]
	 */
	private $keys = array();

	/**
	 * コントローラ内結果データ。
	 *
	 * @var array<string,mixed>
	 */
	protected $result = array();

	/**
	 * Undocumented variable
	 *
	 * @var Validator
	 */
	protected $validator;

	/**
	 * 応答データ。
	 *
	 * @var DataContent|null
	 */
	private $content = null;

	private CookieStore $cookie;
	private TemporaryStore $temporary;
	private SessionStore $session;

	/**
	 * 応答ヘッダ。
	 *
	 * @var array<string,string[]>
	 */
	private array $responseHeaders = array();

	protected function __construct(LogicParameter $parameter)
	{
		$this->httpStatus = HttpStatus::ok();
		$this->request = $parameter->request;
		$this->cookie = $parameter->cookie;
		$this->temporary = $parameter->temporary;
		$this->session = $parameter->session;
		$this->logger = $parameter->logger;

		$this->validator = new Validator($this);
	}

	/**
	 * リクエストデータの取得。
	 *
	 * @param string $key
	 * @param string $default
	 * @param bool $trim
	 * @return string
	 */
	protected function getRequest(string $key, string $default = '', bool $trim = true): string
	{
		if (!$this->request->exists($key)['exists']) {
			return $default;
		}

		$value = $this->request->getValue($key);

		if ($trim) {
			return StringUtility::trim($value);
		}

		return $value;
	}

	protected function getRequestContent(): Bytes
	{
		return FileUtility::readContent('php://input');
	}

	/**
	 * Undocumented function
	 *
	 * @return array<mixed>
	 */
	protected function getRequestJson(): array
	{
		return FileUtility::readJsonFile('php://input');
	}

	protected function setHttpStatus(HttpStatus $httpStatus): void
	{
		$this->httpStatus = $httpStatus;
	}

	public function getHttpStatus(): HttpStatus
	{
		return $this->httpStatus;
	}


	protected function getCookie(string $key, string $default = ''): string
	{
		return $this->cookie->getOr($key, $default);
	}

	/**
	 * Cookie を設定。
	 *
	 * @param string $key
	 * @param string $value
	 * @param CookieOption|array{path:?string,span:?DateInterval,secure:?bool,httpOnly:?bool}|null $option
	 * @return void
	 */
	protected function setCookie(string $key, string $value, $option = null): void
	{
		/** @var CookieOption|null */
		$cookieOption = null;

		if (!is_null($option)) {
			if ($option instanceof CookieOption) {
				$cookieOption = $option;
			} else if (is_array($option)) {
				$cookieOption = CookieOption::create(
					ArrayUtility::getOr($option, 'path', $this->cookie->option->path),
					ArrayUtility::getOr($option, 'span', $this->cookie->option->span),
					ArrayUtility::getOr($option, 'secure', $this->cookie->option->secure),
					ArrayUtility::getOr($option, 'httpOnly', $this->cookie->option->httpOnly),
					ArrayUtility::getOr($option, 'sameSite', $this->cookie->option->sameSite)
				);
			}
		}

		$this->cookie->set($key, $value, $cookieOption);
	}
	protected function removeCookie(string $key): void
	{
		$this->cookie->remove($key);
	}

	protected function peekTemporary(string $key, mixed $default = null): mixed
	{
		$value = $this->temporary->peek($key);
		if (is_null($value)) {
			return $default;
		}

		return $value;
	}

	protected function popTemporary(string $key, mixed $default = null): mixed
	{
		$value = $this->temporary->pop($key);
		if (is_null($value)) {
			return $default;
		}

		return $value;
	}

	protected function pushTemporary(string $key, mixed $value): void
	{
		$this->temporary->push($key, $value);
	}
	protected function removeTemporary(string $key): void
	{
		$this->temporary->remove($key);
	}

	protected function getSession(string $key, mixed $default = null): mixed
	{
		return $this->session->getOr($key, $default);
	}

	protected function setSession(string $key, mixed $value): void
	{
		$this->session->set($key, $value);
	}
	protected function removeSession(string $key): void
	{
		$this->session->remove($key);
	}
	protected function cancelSession(): void
	{
		$this->session->setApplyState(SessionStore::APPLY_CANCEL);
	}
	protected function restartSession(): void
	{
		$this->session->setApplyState(SessionStore::APPLY_RESTART);
	}
	protected function shutdownSession(): void
	{
		$this->session->setApplyState(SessionStore::APPLY_SHUTDOWN);
	}

	public function addResponseHeader(string $name, string $value): void
	{
		if (isset($this->responseHeaders[$name])) {
			$this->responseHeaders[$name][] = $value;
		} else {
			$this->responseHeaders[$name] = [$value];
		}
	}

	/**
	 * パラメータキーの設定。
	 *
	 * @param string[] $keys
	 * @param bool $overwrite キー項目を要求データで上書きするか
	 * @param bool $initialize キー情報を初期化するか
	 * @return void
	 */
	protected function registerParameterKeys(array $keys, bool $overwrite, bool $initialize = true): void
	{
		if ($initialize) {
			$this->keys = $keys;
		} else {
			$this->keys += $keys;
		}

		foreach ($this->keys as $key) {
			if ($overwrite) {
				$value = $this->getRequest($key, '');
				$this->values[$key] = $value;
			} else {
				$this->values[$key] = '';
			}
		}
	}

	/**
	 * 応答データとして設定。
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return void
	 * @throws ArgumentException 入力データとして未登録の場合に投げられる。
	 */
	protected function setValue(string $key, $value): void
	{
		if (ArrayUtility::getCount($this->keys)) {
			if (array_search($key, $this->keys) === false) {
				throw new ArgumentException("key -> $key");
			}
		}

		$this->values[$key] = $value;
	}

	/**
	 * 検証エラーが存在するか。
	 *
	 * @return boolean
	 */
	protected function hasError(): bool
	{
		return 0 < count($this->errors);
	}

	protected function clearErrors(): void
	{
		$this->errors = array();
	}

	protected function removeError(string $key): void
	{
		if (isset($this->errors[$key])) {
			unset($this->errors[$key]);
		}
	}

	protected function addError(string $key, string $message): void
	{
		if (isset($this->errors[$key])) {
			if (array_search($message, $this->errors[$key]) === false) {
				$this->errors[$key][] = $message;
			}
		} else {
			$this->errors[$key] = [$message];
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

	/**
	 * キーに対する一括検証処理。
	 *
	 * @param string $key
	 * @param callable(string $key,string $value):void $callback
	 * @param array{default?:string,trim?:bool}|null $option オプション
	 *   * default: 取得失敗時の値。
	 *   * trim: 値をトリムするか。
	 * @return void
	 */
	protected function validation(string $key, callable $callback, ?array $option = null): void
	{
		$default = ArrayUtility::getOr($option, 'default', '');
		$trim = ArrayUtility::getOr($option, 'trim', true);

		$value = $this->getRequest($key, $default, $trim);
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
	 * 応答ヘッダの取得。
	 *
	 * @return array<string,string[]>
	 */
	public function getResponseHeaders(): array
	{
		return $this->responseHeaders;
	}

	/**
	 * View表示用データの取得。
	 *
	 * @return TemplateParameter
	 */
	public function getViewData(): TemplateParameter
	{
		return new TemplateParameter(
			$this->httpStatus,
			$this->values,
			$this->errors
		);
	}

	/**
	 * 応答データ設定。
	 *
	 * @param string $mime
	 * @param string|array<mixed>|Bytes $data
	 * @return void
	 */
	protected function setContent(string $mime, $data): void
	{
		$this->content = new DataContent(HttpStatus::none(), $mime, $data);
	}

	/**
	 * 応答データ取得。
	 *
	 * @return DataContent
	 * @throws InvalidOperationException 応答データ未設定
	 */
	public function getContent(): DataContent
	{
		if (is_null($this->content)) {
			throw new InvalidOperationException('not impl');
		}

		return new DataContent($this->httpStatus, $this->content->mime, $this->content->data);
	}

	/**
	 * ロジック結果に指定キー項目が存在するか。
	 *
	 * @param string $key
	 * @param mixed $result
	 * @return boolean
	 */
	public function tryGetResult(string $key, &$result): bool
	{
		return ArrayUtility::tryGet($this->result, $key, $result);
	}

	/**
	 * ロジック結果の指定キー項目が指定値に一致するか。
	 *
	 * @param string $key
	 * @param mixed $value
	 * @return boolean
	 */
	public function equalsResult(string $key, $value): bool
	{
		if ($this->tryGetResult($key, $result)) {
			return $result === $value;
		}

		return false;
	}
}
