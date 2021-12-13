<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use \PeServer\Core\FileUtility;
use \PeServer\Core\Log\LoggerBase;
use \PeServer\Core\StringUtility;

class FileLogger extends LoggerBase
{
	private $directoryPath;
	private $baseFileName;

	private static $cleanupHeaders = array();

	public function __construct(string $header, int $level, int $baseTraceIndex, array $fileLoggingConfiguration)
	{
		parent::__construct($header, $level, $baseTraceIndex);

		$this->directoryPath = $fileLoggingConfiguration['dir'];
		$this->baseFileName = $fileLoggingConfiguration['name'];

		if (!in_array($this->header, self::$cleanupHeaders)) {
			$this->cleanup($fileLoggingConfiguration['count']);
			self::$cleanupHeaders[] = $this->header;
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
			$this->baseFileName,
			[
				'HEADER' => $this->toFileSafeNameHeader(),
				'DATE' => '*',
			]
		);
		$logFiles = glob(FileUtility::joinPath($this->directoryPath, $filePattern));
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
			$this->baseFileName,
			[
				'HEADER' => $this->toFileSafeNameHeader(),
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
