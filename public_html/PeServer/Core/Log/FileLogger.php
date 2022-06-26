<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\FileUtility;
use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\Log\LoggerBase;
use PeServer\Core\Throws\Enforce;
use PeServer\Core\Throws\IOException;
use PeServerTest\Core\Throws\EnforceTest;

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
	 * @param string $header ヘッダ。使用用途により意味合いは変わるので実装側でルール決めして使用すること。
	 * @param integer $level 有効レベル。
	 * @param integer $baseTraceIndex 基準トレース位置。
	 * @param array<string,mixed> $fileLoggingConfiguration
	 */
	public function __construct(string $format, string $header, int $level, int $baseTraceIndex, array $fileLoggingConfiguration)
	{
		parent::__construct($format, $header, $level, $baseTraceIndex);

		$directoryPath = ArrayUtility::getOr($fileLoggingConfiguration, 'directory', '');
		Enforce::throwIfNullOrWhiteSpace($directoryPath);
		$this->directoryPath = $directoryPath;

		$baseFileName = ArrayUtility::getOr($fileLoggingConfiguration, 'name', '');
		Enforce::throwIfNullOrWhiteSpace($baseFileName);
		$this->baseFileName = $baseFileName;

		$count = ArrayUtility::getOr($fileLoggingConfiguration, 'count', 0);
		Enforce::throwIf(0 <= $count);
		$this->cleanup($count);
	}

	private function toSafeFileNameHeader(): string
	{
		$trimHeader = StringUtility::trim($this->header, '/\\');
		return str_replace(['/', '\\', '*', '|', '<', '>', '?'], '_', $trimHeader);
	}

	private function cleanupCore(int $maxCount, string $filePattern): void
	{
		$logFiles = glob(FileUtility::joinPath($this->directoryPath, $filePattern));
		if ($logFiles === false) {
			throw new IOException('glob error: ' . FileUtility::joinPath($this->directoryPath, $filePattern));
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
		if (!$maxCount) {
			return;
		}

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
