<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\ArrayUtility;
use PeServer\Core\Code;
use PeServer\Core\IOUtility;
use PeServer\Core\Log\LoggerBase;
use PeServer\Core\PathUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\Throws\Enforce;

/**
 * ファイルロガー。
 */
class FileLogger extends LoggerBase
{
	/**
	 * 出力ディレクトリパス。
	 * @readonly
	 */
	private string $directoryPath;
	/**
	 * ファイル書式名
	 *
	 * @phpstan-var literal-string
	 * @readonly
	 */
	private string $baseFileName;

	/**
	 * 破棄済みファイルパターン。
	 *
	 * @var string[]
	 */
	private static array $cleanupFilePatterns = [];

	/**
	 * 生成。
	 *
	 * @param string $header ヘッダ。使用用途により意味合いは変わるので実装側でルール決めして使用すること。
	 * @phpstan-param non-empty-string $header
	 * @param integer $level 有効レベル。
	 * @phpstan-param ILogger::LOG_LEVEL_* $level 有効レベル。
	 * @param integer $baseTraceIndex 基準トレース位置。
	 * @phpstan-param UnsignedIntegerAlias $baseTraceIndex
	 * @param array<string,mixed> $fileLoggingConfiguration
	 */
	public function __construct(string $format, string $header, int $level, int $baseTraceIndex, array $fileLoggingConfiguration)
	{
		parent::__construct($format, $header, $level, $baseTraceIndex);

		$directoryPath = ArrayUtility::getOr($fileLoggingConfiguration, 'directory', '');
		Enforce::throwIfNullOrWhiteSpace($directoryPath);
		$this->directoryPath = $directoryPath;

		$baseFileName = Code::toLiteralString(ArrayUtility::getOr($fileLoggingConfiguration, 'name', ''));
		Enforce::throwIfNullOrWhiteSpace($baseFileName);
		$this->baseFileName = $baseFileName;

		$count = ArrayUtility::getOr($fileLoggingConfiguration, 'count', 0);
		Enforce::throwIf(0 <= $count);
		$this->cleanup($count);
	}

	private function toSafeFileNameHeader(): string
	{
		$trimHeader = StringUtility::trim($this->header, '/\\');
		return StringUtility::replace($trimHeader, ['/', '\\', '*', '|', '<', '>', '?'], '_');
	}

	protected function toHeaderDate(bool $isCleanup): string
	{
		return $isCleanup
			? '*'
			: date('Ymd');
	}

	/**
	 * 破棄処理内部実装。
	 *
	 * @param int $maxCount
	 * @phpstan-param UnsignedIntegerAlias $maxCount
	 * @param string $filePattern
	 */
	private function cleanupCore(int $maxCount, string $filePattern): void
	{
		$logFiles = IOUtility::find($this->directoryPath, $filePattern);
		$logCount = ArrayUtility::getCount($logFiles);
		if ($logCount <= $maxCount) {
			return;
		}

		$length = $logCount - $maxCount;
		for ($i = 0; $i < $length; $i++) {
			IOUtility::removeFile($logFiles[$i]);
		}
	}

	/**
	 * 破棄処理。
	 *
	 * @param int $maxCount
	 * @phpstan-param UnsignedIntegerAlias $maxCount
	 */
	private function cleanup(int $maxCount): void
	{
		if (!$maxCount) {
			return;
		}

		$filePattern = StringUtility::replaceMap(
			$this->baseFileName,
			[
				'HEADER' => $this->toSafeFileNameHeader(),
				'DATE' => $this->toHeaderDate(true),
			]
		);
		if (!ArrayUtility::containsValue(self::$cleanupFilePatterns, $filePattern)) {
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
				'DATE' => $this->toHeaderDate(false),
			]
		);

		return PathUtility::combine($this->directoryPath, $fileName);
	}

	protected function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		IOUtility::createDirectoryIfNotExists($this->directoryPath);

		$logMessage = $this->format($level, $traceIndex + 1, $message, ...$parameters);

		$filePath = $this->getLogFilePath();
		//error_logが制限されている場合はこっちを使用する→: file_put_contents($filePath, $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
		error_log($logMessage . PHP_EOL, 3, $filePath);
	}
}
