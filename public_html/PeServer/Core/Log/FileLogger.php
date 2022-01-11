<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\ArrayUtility;
use PeServer\Core\FileUtility;
use PeServer\Core\Log\LoggerBase;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\CoreError;

class FileLogger extends LoggerBase
{
	/**
	 * 出力ディレクトリパス。
	 */
	private string $directoryPath;
	/**
	 * ファイル書式名
	 */
	private string $baseFileName;

	/**
	 * 破棄済みファイルパターン。
	 *
	 * @var string[]
	 */
	private static array $cleanupFilePatterns = array();

	/**
	 * 生成。
	 *
	 * @param string $header
	 * @param integer $level
	 * @param integer $baseTraceIndex
	 * @param array<string,mixed> $fileLoggingConfiguration
	 */
	public function __construct(string $header, int $level, int $baseTraceIndex, array $fileLoggingConfiguration)
	{
		parent::__construct($header, $level, $baseTraceIndex);

		$this->directoryPath = $fileLoggingConfiguration['directory'];
		$this->baseFileName = $fileLoggingConfiguration['name'];

		$this->cleanup($fileLoggingConfiguration['count']);
	}

	private function toSafeFileNameHeader(): string
	{
		$trimHeader = trim($this->header, '/\\');
		return str_replace(['/', '\\', '*', '|', '<', '>', '?'], '_', $trimHeader);
	}

	private function cleanupCore(int $maxCount, string $filePattern): void
	{
		$logFiles = glob(FileUtility::joinPath($this->directoryPath, $filePattern));
		if ($logFiles === false) {
			throw new CoreError('glob error: ' . FileUtility::joinPath($this->directoryPath, $filePattern));
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

	private function cleanup(int $maxCount): void
	{
		$filePattern = StringUtility::replaceMap(
			$this->baseFileName,
			[
				'HEADER' => $this->toSafeFileNameHeader(),
				'DATE' => '*',
			]
		);
		if (!ArrayUtility::contains(self::$cleanupFilePatterns, $filePattern)) {
			self::$cleanupFilePatterns[] = $filePattern;
			$this->cleanupCore($maxCount, $filePattern);
		}
	}

	private function getLogFilePath(): string
	{
		$fileName = StringUtility::replaceMap(
			$this->baseFileName,
			[
				'HEADER' => $this->toSafeFileNameHeader(),
				'DATE' => date('Ymd'),
			]
		);

		return FileUtility::joinPath($this->directoryPath, $fileName);
	}

	protected function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		FileUtility::createDirectoryIfNotExists($this->directoryPath);

		$logMessage = $this->format($level, $traceIndex + 1, $message, ...$parameters);

		$filePath = $this->getLogFilePath();
		//error_logが制限されている場合はこっちを使用する→: file_put_contents($filePath, $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
		error_log($logMessage . PHP_EOL, 3, $filePath);
	}
}
