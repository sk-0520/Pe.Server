<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Exception;
use \LogicException;
use \PeServer\Core\LogicParameter;

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
	 * @var array<string,string|array>
	 */
	private $errors = array();
	/**
	 * 応答データ。
	 *
	 * @var array<string,string|array>
	 */
	private $values = array();

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

	/**
	 * 検証ロジック実装。
	 *
	 * @param integer $logicMode LogicMode 参照のこと
	 * @return void
	 */
	protected abstract function validateImpl(int $logicMode): void;
	/**
	 * 実行ロジック実装。
	 *
	 * @param integer $logicMode LogicMode 参照のこと
	 * @return void
	 */
	protected abstract function executeImpl(int $logicMode): void;

	private function validate(int $logicMode)
	{
		$this->validateImpl($logicMode);
	}

	private function execute(int $logicMode)
	{
		$this->executeImpl($logicMode);
	}

	/**
	 * ロジック処理。
	 *
	 * @param integer $logicMode LogicMode 参照のこと
	 * @return boolean
	 */
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

	/**
	 * View表示用データの取得。
	 *
	 * @return array
	 */
	public function getViewData(): array
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
	 * @throws LogicException 応答データ未設定
	 */
	public function getResponse(): ActionResponse
	{
		if (is_null($this->response)) {
			throw new LogicException('not impl');
		}

		return $this->response;
	}
}
