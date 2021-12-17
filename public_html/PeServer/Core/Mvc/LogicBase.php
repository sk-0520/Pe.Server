<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

use \PeServer\Core\ILogger;
use \PeServer\Core\ActionRequest;
use \PeServer\Core\ActionResponse;
use \PeServer\Core\HttpStatusCode;
use \PeServer\Core\Mvc\LogicParameter;
use \PeServer\Core\Throws\InvalidOperationException;

/**
 * コントローラから呼び出されるロジック基底処理。
 */
abstract class LogicBase
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
	private $request;

	/**
	 * HTTPステータスコード。
	 *
	 * @var int
	 */
	private $statusCode = HttpStatusCode::OK;
	/**
	 * 検証エラー。
	 *
	 * @var array<string,string[]>
	 */
	private $errors = array();
	/**
	 * 応答データ。
	 *
	 * @var array<string,string|array>
	 */
	private $values = array(); // @phpstan-ignore-line

	/**
	 * 応答データ。
	 *
	 * @var ActionResponse|null
	 */
	private $response = null;

	protected function __construct(LogicParameter $parameter)
	{
		$this->request = $parameter->request;
		$this->logger = $parameter->logger;

		$this->logger->trace('LOGIC');
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
		if (!$this->request->exists($key)['exists']) {
			return $default;
		}
		return $this->request->getValue($key);
	}

	/**
	 * 検証エラーが存在するか。
	 *
	 * @return boolean
	 */
	public function hasError(): bool
	{
		return 0 < count($this->errors);
	}

	protected function addError(string $key, string $message): void
	{
		if (isset($this->errors[$key])) {
			$this->errors[$key] = [$message];
		} else {
			$this->errors[$key][] = $message;
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
			'status' => $this->statusCode,
			'errors' => $this->errors,
			'values' => $this->values,
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
		$this->response = $response;
	}

	/**
	 * 応答データ取得。
	 *
	 * @return ActionResponse
	 * @throws InvalidOperationException 応答データ未設定
	 */
	public function getResponse(): ActionResponse
	{
		if (is_null($this->response)) {
			throw new InvalidOperationException('not impl');
		}

		return $this->response;
	}
}
