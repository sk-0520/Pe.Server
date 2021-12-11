<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use \PeServer\Core\FileUtility;
use \PeServer\Core\Log\LoggerBase;

class FileLogger extends LoggerBase
{
	//private $fileLoggingConfiguration;

	private $directoryPath;
	private $baseFileName;

	public function __construct(string $header, int $level, int $baseTraceIndex, array $fileLoggingConfiguration)
	{
		parent::__construct($header, $level, $baseTraceIndex);

		$this->directoryPath = $fileLoggingConfiguration['dir'];
		$this->baseFileName = $fileLoggingConfiguration['file'];

		$this->cleanup($fileLoggingConfiguration['count']);
	}

	private function cleanup(int $maxCount): void
	{
		//TODO: 掃除
	}

	private function getLogFilePath(): string
	{
		//TODO: 日付系
		return FileUtility::joinPath($this->directoryPath, $this->baseFileName);
	}

	protected function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		FileUtility::createDirectoryIfNotExists($this->directoryPath);

		$logMessage = $this->format($level, $traceIndex + 1, $message, ...$parameters);

		$filePath = $this->getLogFilePath();
		file_put_contents($filePath, $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
	}
}
