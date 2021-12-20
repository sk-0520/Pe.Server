<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use \PeServer\Core\FileUtility;
use \PeServer\Core\Log\LoggerBase;
use \PeServer\Core\StringUtility;
use \PeServer\Core\Throws\CoreError;

class FileLogger extends LoggerBase
{
	/**
	 * 出力ディレクトリパス。
	 *
	 * @var string
	 */
	private $_directoryPath;
	/**
	 * ファイル書式名
	 *
	 * @var string
	 */
	private $_baseFileName;

	/**
	 * 破棄済みヘッダ名。
	 *
	 * @var string[]
	 */
	private static $_cleanupHeaders = array();

	public function __construct(string $header, int $level, int $baseTraceIndex, array $fileLoggingConfiguration) // @phpstan-ignore-line
	{
		parent::__construct($header, $level, $baseTraceIndex);

		$this->_directoryPath = $fileLoggingConfiguration['dir'];
		$this->_baseFileName = $fileLoggingConfiguration['name'];

		if (!in_array($this->header, self::$_cleanupHeaders)) {
			$this->cleanup($fileLoggingConfiguration['count']);
			self::$_cleanupHeaders[] = $this->header;
		}
	}

	private function toFileSafeNameHeader(): string
	{
		$trimHeader = trim($this->header, '/\\');
		return str_replace(['/', '\\', '*', '|', '<', '>', '?'], '_', $trimHeader);
	}

	private function cleanup(int $maxCount): void
	{
		$filePattern = StringUtility::replaceMap(
			$this->_baseFileName,
			[
				'HEADER' => $this->toFileSafeNameHeader(),
				'DATE' => '*',
			]
		);
		$logFiles = glob(FileUtility::joinPath($this->_directoryPath, $filePattern));
		if($logFiles === false) {
			throw new CoreError('glob error: ' . FileUtility::joinPath($this->_directoryPath, $filePattern));
		}
		$logCount = count($logFiles);
		if ($logCount <= $maxCount) {
			return;
		}

		$length = $logCount - $maxCount;
		for ($i = 0; $i < $length; $i++) {
			unlink($logFiles[$i]);
		}
	}

	private function getLogFilePath(): string
	{
		$fileName = StringUtility::replaceMap(
			$this->_baseFileName,
			[
				'HEADER' => $this->toFileSafeNameHeader(),
				'DATE' => date('Ymd'),
			]
		);

		return FileUtility::joinPath($this->_directoryPath, $fileName);
	}

	protected function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		FileUtility::createDirectoryIfNotExists($this->_directoryPath);

		$logMessage = $this->format($level, $traceIndex + 1, $message, ...$parameters);

		$filePath = $this->getLogFilePath();
		//error_logが制限されている場合はこっちを使用する→: file_put_contents($filePath, $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
		error_log($logMessage . PHP_EOL, 3, $filePath);
	}
}
