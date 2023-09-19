<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use DateInterval;
use DateTimeImmutable;
use PeServer\Core\Binary;
use PeServer\Core\Collections\Arr;
use PeServer\Core\Http\HttpRequest;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\I18n;
use PeServer\Core\IO\File;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\Log\ILogger;
use PeServer\Core\Mime;
use PeServer\Core\Mvc\DataContent;
use PeServer\Core\Mvc\IValidationReceiver;
use PeServer\Core\Mvc\LogicCallMode;
use PeServer\Core\Mvc\LogicParameter;
use PeServer\Core\Mvc\Template\TemplateParameter;
use PeServer\Core\Mvc\UploadFile;
use PeServer\Core\Mvc\Validator;
use PeServer\Core\Store\CookieOption;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\Stores;
use PeServer\Core\Store\Template\TemporaryStore;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Throws\KeyNotFoundException;
use PeServer\Core\Utc;

/**
 * コントローラから呼び出されるロジック基底処理。
 */
abstract class LogicBase implements IValidationReceiver
{
	#region define

	protected const SESSION_ALL_CLEAR = Text::EMPTY;

	#endregion

	#region variable

	/**
	 * ロジック開始日時。
	 *
	 * @readonly
	 */
	protected DateTimeImmutable $beginTimestamp;

	/**
	 * ロガー。
	 * @readonly
	 */
	protected ILogger $logger;
	/**
	 * リクエストデータ。
	 * @readonly
	 */
	private HttpRequest $request;

	/**
	 * HTTPステータスコード。
	 */
	private HttpStatus $httpResponseStatus;
	/**
	 * 検証エラー。
	 *
	 * @var array<string,string[]>
	 */
	private array $errors = [];
	/**
	 * 応答データ。
	 *
	 * @var array<non-empty-string,mixed>
	 */
	private array $values = [];

	/**
	 * 要素設定がなされている場合に応答データのキーをこの項目に固定。
	 *
	 * @var string[]
	 * @phpstan-var non-empty-string[]
	 */
	private array $keys = [];

	/**
	 * コントローラ内結果データ。
	 *
	 * @var array<string,mixed>
	 */
	protected array $result = [];

	/**
	 * 検証処理。
	 */
	protected Validator $validator;

	/**
	 * 応答データ。
	 */
	private ?DataContent $content = null;

	/**
	 * @readonly
	 */
	protected Stores $stores;

	/**
	 * 応答ヘッダ。
	 *
	 * @var array<string,string[]>
	 * @phpstan-var array<non-empty-string,string[]>
	 */
	private array $responseHeaders = [];

	#endregion

	/**
	 * 生成。
	 *
	 * @param LogicParameter $parameter ロジック用パラメータ。
	 */
	protected function __construct(LogicParameter $parameter)
	{
		$this->beginTimestamp = Utc::create();
		$this->httpResponseStatus = HttpStatus::OK;
		$this->request = $parameter->request;
		$this->stores = $parameter->stores;
		$this->logger = $parameter->logger;

		$this->validator = new Validator($this);
	}

	#region function

	/**
	 * 要求データの取得。
	 *
	 * @param string $key 要求キー。
	 * @param string $fallbackValue 要求キーに存在しない場合の戻り値。
	 * @param bool $trim 取得データをトリムするか。
	 * @return string 要求データ。
	 */
	protected function getRequest(string $key, string $fallbackValue = Text::EMPTY, bool $trim = true): string
	{
		if (!$this->request->exists($key)->exists) {
			return $fallbackValue;
		}

		$value = $this->request->getValue($key);

		if ($trim) {
			return Text::trim($value);
		}

		return $value;
	}

	protected function getFile(string $key): UploadFile
	{
		if (!$this->request->exists($key, true)->exists) {
			throw new InvalidOperationException('$key: ' . $key);
		}

		$file = $this->request->getFile($key);
		return $file;
	}

	/**
	 * 要求本文の生データを取得。
	 *
	 * @return Binary
	 */
	protected function getRequestContent(): Binary
	{
		return File::readContent('php://input');
	}

	/**
	 * 要求本文から JSON を取得。
	 *
	 * @return array<mixed>
	 */
	protected function getRequestJson(): array
	{
		return File::readJsonFile('php://input');
	}

	/**
	 * HTTP応答ステータスコードの設定。
	 *
	 * @param HttpStatus $httpStatus HTTPステータスコード。
	 * @return void
	 */
	protected function setHttpStatus(HttpStatus $httpStatus): void
	{
		$this->httpResponseStatus = $httpStatus;
	}

	/**
	 * HTTP応答ステータスコードの取得。
	 *
	 * @return HttpStatus
	 */
	public function getHttpStatus(): HttpStatus
	{
		return $this->httpResponseStatus;
	}

	/**
	 * Cookie を取得。
	 *
	 * @param string $key キー。
	 * @param string $fallbackValue 取得失敗時の値。
	 * @return string
	 */
	protected function getCookie(string $key, string $fallbackValue = Text::EMPTY): string
	{
		return $this->stores->cookie->getOr($key, $fallbackValue);
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

		if ($option !== null) {
			if ($option instanceof CookieOption) {
				$cookieOption = $option;
			} else {
				/** @var string */
				$path = Arr::getOr($option, 'path', $this->stores->cookie->option->path);
				/** @var \DateInterval|null */
				$span = Arr::getOr($option, 'span', $this->stores->cookie->option->span);
				/** @var bool */
				$secure = Arr::getOr($option, 'secure', $this->stores->cookie->option->secure);
				/** @var bool */
				$httpOnly = Arr::getOr($option, 'httpOnly', $this->stores->cookie->option->httpOnly);
				/**
				 * @var string
				 * @phpstan-var 'Lax'|'lax'|'None'|'none'|'Strict'|'strict'
				 */
				$sameSite = Arr::getOr($option, 'sameSite', $this->stores->cookie->option->sameSite);

				$cookieOption = new CookieOption(
					$path,
					$span,
					$secure,
					$httpOnly,
					$sameSite
				);
			}
		}

		$this->stores->cookie->set($key, $value, $cookieOption);
	}

	/**
	 * Cookie を削除。
	 *
	 * @param string $key キー。
	 * @return void
	 */
	protected function removeCookie(string $key): void
	{
		$this->stores->cookie->remove($key);
	}

	protected function peekTemporary(string $key, mixed $fallbackValue = null): mixed
	{
		$value = $this->stores->temporary->peek($key);
		if ($value === null) {
			return $fallbackValue;
		}

		return $value;
	}

	protected function popTemporary(string $key, mixed $fallbackValue = null): mixed
	{
		$value = $this->stores->temporary->pop($key);
		if ($value === null) {
			return $fallbackValue;
		}

		return $value;
	}

	protected function pushTemporary(string $key, mixed $value): void
	{
		$this->stores->temporary->push($key, $value);
	}
	protected function removeTemporary(string $key): void
	{
		$this->stores->temporary->remove($key);
	}

	protected function existsSession(string $key): bool
	{
		return $this->stores->session->tryGet($key, $unused);
	}

	protected function getSession(string $key, mixed $fallbackValue = null): mixed
	{
		return $this->stores->session->getOr($key, $fallbackValue);
	}

	protected function requireSession(string $key): mixed
	{
		if ($this->stores->session->tryGet($key, $result)) {
			return $result;
		}

		throw new KeyNotFoundException($key);
	}


	protected function setSession(string $key, mixed $value): void
	{
		$this->stores->session->set($key, $value);
	}
	protected function removeSession(string $key): void
	{
		$this->stores->session->remove($key);
	}
	protected function cancelSession(): void
	{
		$this->stores->session->setApplyState(SessionStore::APPLY_CANCEL);
	}
	protected function restartSession(): void
	{
		$this->stores->session->setApplyState(SessionStore::APPLY_RESTART);
	}
	protected function shutdownSession(): void
	{
		$this->stores->session->setApplyState(SessionStore::APPLY_SHUTDOWN);
	}

	/**
	 * 応答HTTPヘッダ追加。
	 *
	 * @param string $name
	 * @phpstan-param non-empty-string $name
	 * @param string $value
	 * @return void
	 */
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
	 * @phpstan-param non-empty-string[] $keys
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
				$value = $this->getRequest($key, Text::EMPTY);
				$this->values[$key] = $value;
			} else {
				$this->values[$key] = Text::EMPTY;
			}
		}
	}

	/**
	 * 応答データとして設定。
	 *
	 * @param string $key
	 * @phpstan-param non-empty-string $key
	 * @param mixed $value
	 * @return void
	 * @throws ArgumentException 入力データとして未登録の場合に投げられる。
	 */
	protected function setValue(string $key, $value): void
	{
		if (Arr::getCount($this->keys)) {
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
		$this->errors = [];
	}

	protected function removeError(string $key): void
	{
		if (isset($this->errors[$key])) {
			unset($this->errors[$key]);
		}
	}

	protected function addCommonError(string $message): void
	{
		$this->addError(Validator::COMMON, $message);
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

	/**
	 * キーに対する一括検証処理。
	 *
	 * @param string $key
	 * @param callable $callback
	 * @phpstan-param callable(string,string):void $callback
	 * @param array{default?:string,trim?:bool}|null $option オプション
	 *   * default: 取得失敗時の値。
	 *   * trim: 値をトリムするか。
	 * @return void
	 */
	protected function validation(string $key, callable $callback, ?array $option = null): void
	{
		/** @var string */
		$default = Arr::getOr($option, 'default', Text::EMPTY);
		/** @var bool */
		$trim = Arr::getOr($option, 'trim', true);

		$value = $this->getRequest($key, $default, $trim);
		$callback($key, $value);
	}

	/**
	 * 検証ロジック実装。
	 *
	 * @param LogicCallMode $callMode 呼び出し。
	 * @return void
	 */
	abstract protected function validateImpl(LogicCallMode $callMode): void;

	/**
	 * 実行ロジック実装。
	 *
	 * @param LogicCallMode $callMode 呼び出し。
	 * @return void
	 */
	abstract protected function executeImpl(LogicCallMode $callMode): void;

	protected function startup(LogicCallMode $callMode): void
	{
		//NOP
	}

	protected function cleanup(LogicCallMode $callMode): void
	{
		//NOP
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
	 * @phpstan-return array<non-empty-string,string[]>
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
			$this->httpResponseStatus,
			$this->values,
			$this->errors
		);
	}

	final protected function setTextContent(string $data): void
	{
		$this->setContent(Mime::TEXT, $data);
	}

	/**
	 * JSON応答データ設定。
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
	 * @phpstan-param non-empty-string|\PeServer\Core\Mime::* $mime
	 * @param string|array<mixed>|Binary $data
	 * @return void
	 */
	protected function setContent(string $mime, $data): void
	{
		$this->content = new DataContent(HttpStatus::None, $mime, $data);
	}

	/**
	 * ファイルを応答として設定。
	 *
	 * @param string|null $mime
	 * @phpstan-param Mime::*|null $mime
	 * @param string $path
	 */
	protected function setFileContent(?string $mime, string $path): void
	{
		if (Text::isNullOrWhiteSpace($mime)) {
			$mime = Mime::fromFileName($path);
		}
		/** @phpstan-var non-empty-string $mime */

		$content = File::readContent($path);

		$this->content = new DataContent(HttpStatus::None, $mime, $content->getRaw());
	}

	/**
	 * ダウンロードデータ応答。
	 *
	 * @param string $mime
	 * @phpstan-param non-empty-string|\PeServer\Core\Mime::* $mime
	 * @param string $fileName
	 * @param Binary $data
	 * @return void
	 */
	final protected function setDownloadContent(string $mime, string $fileName, Binary $data): void
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
		if ($this->content === null) {
			throw new InvalidOperationException();
		}

		if ($this->content instanceof DownloadDataContent) {
			return $this->content;
		}

		return new DataContent($this->httpResponseStatus, $this->content->mime, $this->content->data);
	}

	/**
	 * ロジック結果に指定キー項目が存在するか。
	 *
	 * @template TValue
	 * @param string $key
	 * @param mixed $result
	 * @phpstan-param TValue $result
	 * @return boolean
	 */
	public function tryGetResult(string $key, &$result): bool
	{
		return Arr::tryGet($this->result, $key, $result);
	}

	/**
	 * ロジック結果の指定キー項目が指定値に一致するか。
	 *
	 * @template TValue
	 * @param string $key
	 * @param mixed $value
	 * @phpstan-param TValue $value
	 * @return boolean
	 */
	public function equalsResult(string $key, $value): bool
	{
		if ($this->tryGetResult($key, $result)) {
			return $result === $value;
		}

		return false;
	}

	#endregion

	#region IValidationReceiver

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

	#endregion
}
