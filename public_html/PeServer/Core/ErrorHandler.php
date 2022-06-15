<?php

declare(strict_types=1);

namespace PeServer\Core;

use \Throwable;
use PeServer\Core\Log\Logging;
use PeServer\Core\Mvc\Template;
use PeServer\Core\Throws\Throws;
use PeServer\Core\Http\HttpStatus;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\Throws\HttpStatusException;
use PeServer\Core\Throws\InvalidOperationException;

/**
 * エラーハンドリング処理。
 */
class ErrorHandler
{
	/** 登録済みか。 */
	private bool $isRegistered = false;

	/**
	 * 抑制エラーコード指定。
	 *
	 * @return HttpStatus[]
	 */
	protected function getSuppressionStatusList(): array
	{
		return [
			HttpStatus::notFound(),
		];
	}

	/**
	 * エラーハンドラの登録処理。
	 *
	 * 明示的に呼び出しが必要。
	 *
	 * @return void
	 */
	public final function register(): void
	{
		if ($this->isRegistered) {
			throw new InvalidOperationException();
		}

		register_shutdown_function([$this, 'receiveShutdown']);
		set_exception_handler([$this, 'receiveException']);
		set_error_handler([$this, 'receiveError']);
		$this->isRegistered = true;
	}

	/**
	 * シャットダウン処理でエラーがあれば処理する。
	 */
	public final function receiveShutdown(): void
	{
		$lastError = error_get_last();
		if (is_null($lastError)) {
			return;
		}

		/** @var int */
		$type = ArrayUtility::getOr($lastError, 'type', -1);
		/** @var string */
		$message = ArrayUtility::getOr($lastError, 'message', '');
		/** @var string */
		$file = ArrayUtility::getOr($lastError, 'file', '<unknown>');
		/** @var int */
		$line = ArrayUtility::getOr($lastError, 'line', 0);

		$this->_catchError(
			$type,
			$message,
			$file,
			$line,
			null
		);
	}

	/**
	 * 未ハンドル例外を処理する。
	 *
	 * @param Throwable $throwable
	 * @return no-return
	 */
	public final function receiveException(Throwable $throwable)
	{
		$this->_catchError(
			Throws::getErrorCode($throwable),
			$throwable->getMessage(),
			$throwable->getFile(),
			$throwable->getLine(),
			$throwable
		);
	}

	/**
	 * エラーを処理する。
	 *
	 * @param integer $errorNumber
	 * @param string $errorMessage
	 * @param string $errorFile
	 * @param int $errorLineNumber
	 * @return no-return
	 */
	public final function receiveError(int $errorNumber, string $errorMessage, string $errorFile, int $errorLineNumber/* , array $_ */)
	{
		$this->_catchError(
			$errorNumber,
			$errorMessage,
			$errorFile,
			$errorLineNumber,
			null
		);
	}

	/**
	 * 例外からHTTP応答ステータスコードを設定する。
	 *
	 * @param Throwable|null $throwable
	 * @return HttpStatus 設定されたHTTPステータスコード。
	 */
	protected final function setHttpStatus(?Throwable $throwable): HttpStatus
	{
		$status = $throwable instanceof HttpStatusException
			? $throwable->status
			: HttpStatus::serviceUnavailable();

		http_response_code($status->getCode());

		return $status;
	}

	/**
	 * エラー取得処理（呼び出し側）。
	 *
	 * @param integer $errorNumber
	 * @param string $message
	 * @param string $file
	 * @param integer $lineNumber
	 * @param Throwable|null $throwable
	 * @return no-return
	 */
	private function _catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable)
	{
		$this->catchError($errorNumber, $message, $file, $lineNumber, $throwable);
		exit;
	}

	/**
	 * エラー取得処理（本体）。
	 *
	 * @param integer $errorNumber
	 * @param string $message
	 * @param string $file
	 * @param integer $lineNumber
	 * @param Throwable|null $throwable
	 * @return void
	 */
	protected function catchError(int $errorNumber, string $message, string $file, int $lineNumber, ?Throwable $throwable): void
	{
		$values = [
			'error_number' => $errorNumber,
			'message' => $message,
			'file' => $file,
			'line_number' => $lineNumber,
			'throwable' => $throwable,
		];

		$status = $this->setHttpStatus($throwable);

		$logger = Logging::create(__CLASS__);

		$isSuppressionStatus = false;
		foreach ($this->getSuppressionStatusList() as $suppressionStatus) {
			if ($status->is($suppressionStatus)) {
				$isSuppressionStatus = true;
				$logger->info('HTTP: ' . $suppressionStatus->getCode());
				break;
			}
		}
		if (!$isSuppressionStatus) {
			$logger->error($values);
		}

		$template = Template::create('template', 'Core');
		echo $template->build('error-display.tpl', new TemplateParameter($status, $values, []));
	}
}
