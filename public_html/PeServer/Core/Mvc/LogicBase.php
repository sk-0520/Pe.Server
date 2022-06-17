<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \DateInterval;
use PeServer\Core\I18n;
use PeServer\Core\Mime;
use PeServer\Core\Bytes;
use PeServer\Core\ILogger;
use PeServer\Core\FileUtility;
use PeServer\Core\ArrayUtility;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\StringUtility;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\DataContent;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\TemporaryStore;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\Mvc\IValidationReceiver;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * コントローラから呼び出されるロジック基底処理。
 */
abstract class LogicBase implements IValidationReceiver
{
	protected const SESSION_ALL_CLEAR = '';

	/**
	 * ロガー。
	 */
	protected ILogger $logger;
	/**
	 * リクエストデータ。
	 */
	private HttpRequest $request;

	/**
	 * HTTPステータスコード。
	 */
	private HttpStatus $httpStatus;
	/**
	 * 検証エラー。
	 *
	 * @var array<string,string[]>
	 */
	private array $errors = array();
	/**
	 * 応答データ。
	 *
	 * @var array<string,mixed>
	 */
	private array $values = array();

	/**
	 * 要素設定がなされている場合に応答データのキーをこの項目に固定。
	 *
	 * @var string[]
	 */
	private array $keys = array();

	/**
	 * コントローラ内結果データ。
	 *
	 * @var array<string,mixed>
	 */
	protected array $result = array();

	/**
	 * 検証処理。
	 */
	protected Validator $validator;

	/**
	 * 応答データ。
	 */
	private ?DataContent $content = null;

	/** cookie 管理 */
	private CookieStore $cookie;
	/** 一時データ管理 */
	private TemporaryStore $temporary;
	/** セッション管理 */
	private SessionStore $session;

	/**
	 * 応答ヘッダ。
	 *
	 * @var array<string,string[]>
	 */
	private array $responseHeaders = array();

	/**
	 * 生成。
	 *
	 * @param LogicParameter $parameter ロジック用パラメータ。
	 */
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
	 * 要求データの取得。
	 *
	 * @param string $key 要求キー。
	 * @param string $fallbackValue 要求キーに存在しない場合の戻り値。
	 * @param bool $trim 取得データをトリムするか。
	 * @return string 要求データ。
	 */
	protected function getRequest(string $key, string $fallbackValue = '', bool $trim = true): string
	{
		if (!$this->request->exists($key)['exists']) {
			return $fallbackValue;
		}

		$value = $this->request->getValue($key);

		if ($trim) {
			return StringUtility::trim($value);
		}

		return $value;
	}

	/**
	 * 要求本文の生データを取得。
	 *
	 * @return Bytes
	 */
	protected function getRequestContent(): Bytes
	{
		return FileUtility::readContent('php://input');
	}

	/**
	 * 要求本文から JSON を取得。
	 *
	 * @return array<mixed>
	 */
	protected function getRequestJson(): array
	{
		return FileUtility::readJsonFile('php://input');
	}

	/**
	 * HTTP応答ステータスコードの設定。
	 *
	 * @param HttpStatus $httpStatus HTTPステータスコード。
	 * @return void
	 */
	protected function setHttpStatus(HttpStatus $httpStatus): void
	{
		$this->httpStatus = $httpStatus;
	}

	/**
	 * HTTP応答ステータスコードの取得。
	 *
	 * @return HttpStatus
	 */
	public function getHttpStatus(): HttpStatus
	{
		return $this->httpStatus;
	}

	/**
	 * Cookie を取得。
	 *
	 * @param string $key キー。
	 * @param string $fallbackValue 取得失敗時の値。
	 * @return string
	 */
	protected function getCookie(string $key, string $fallbackValue = ''): string
	{
		return $this->cookie->getOr($key, $fallbackValue);
	}

	/**
	 * Cookie を設定。
	 *
	 * @param string $key キー。
	 * @param string $value 設定値。
	 * @param CookieOption|array{path:?string,span:?DateInterval,secure:?bool,httpOnly:?bool}|null $option オプション。
	 * @return void
	 */
	protected function setCookie(string $key, string $value, CookieOption|array|null $option = null): void
	{
		/** @var CookieOption|null */
		$cookieOption = null;

		if (!is_null($option)) {
			if ($option instanceof CookieOption) {
				$cookieOption = $option;
			} else {
				/** @var string */
				$path = ArrayUtility::getOr($option, 'path', $this->cookie->option->path);
				/** @var \DateInterval|null */
				$span = ArrayUtility::getOr($option, 'span', $this->cookie->option->span);
				/** @var bool */
				$secure = ArrayUtility::getOr($option, 'secure', $this->cookie->option->secure);
				/** @var bool */
				$httpOnly = ArrayUtility::getOr($option, 'httpOnly', $this->cookie->option->httpOnly);
				/** @var 'Lax'|'lax'|'None'|'none'|'Strict'|'strict' */
				$sameSite = ArrayUtility::getOr($option, 'sameSite', $this->cookie->option->sameSite);

				$cookieOption = new CookieOption(
					$path,
					$span,
					$secure,
					$httpOnly,
					(string)$sameSite
				);
			}
		}

		$this->cookie->set($key, $value, $cookieOption);
	}

	/**
	 * Cookie を削除。
	 *
	 * @param string $key キー。
	 * @return void
	 */
	protected function removeCookie(string $key): void
	{
		$this->cookie->remove($key);
	}

	protected function peekTemporary(string $key, mixed $fallbackValue = null): mixed
	{
		$value = $this->temporary->peek($key);
		if (is_null($value)) {
			return $fallbackValue;
		}

		return $value;
	}

	protected function popTemporary(string $key, mixed $fallbackValue = null): mixed
	{
		$value = $this->temporary->pop($key);
		if (is_null($value)) {
			return $fallbackValue;
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

	protected function getSession(string $key, mixed $fallbackValue = null): mixed
	{
		return $this->session->getOr($key, $fallbackValue);
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
				throw new ArgumentException("未登録 key -> $key");
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
		/** @var string */
		$default = ArrayUtility::getOr($option, 'default', '');
		/** @var bool */
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

	final protected function setTextContent(string $data): void
	{
		$this->setContent(Mime::TEXT, $data);
	}

	/**
	 * Undocumented function
	 *
	 * @param array<mixed> $data
	 * @return void
	 */
	final protected function setJsonContent(array $data): void
	{
		$this->setContent(Mime::JSON, $data);
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

	protected function setDownloadContent(string $mime, string $fileName, Bytes $data): void
	{
		$this->content = new DownloadDataContent($mime, $fileName, $data);
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
			throw new InvalidOperationException();
		}

		if ($this->content instanceof DownloadDataContent) {
			return $this->content;
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
