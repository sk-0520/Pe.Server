<?php

declare(strict_types=1);

namespace PeServer\Core\Log;

use PeServer\Core\Code;
use PeServer\Core\Collections\Arr;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;
use PeServer\Core\Log\LoggerBase;
use PeServer\Core\Log\LogOptions;
use PeServer\Core\Text;
use PeServer\Core\Throws\Enforce;

/**
 * ファイルロガー。
 */
class FileLogger extends LoggerBase
{
	#region variable

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

	#endregion

	/**
	 * 生成。
	 *
	 * @param LogOptions $options
	 */
	public function __construct(LogOptions $options)
	{
		parent::__construct($options);

		$directoryPath = Arr::getOr($this->options->configuration, 'directory', '');
		Enforce::throwIfNullOrWhiteSpace($directoryPath);
		$this->directoryPath = $directoryPath;

		$baseFileName = Code::toLiteralString(Arr::getOr($this->options->configuration, 'name', ''));
		Enforce::throwIfNullOrWhiteSpace($baseFileName);
		$this->baseFileName = $baseFileName;

		/** @phpstan-var UnsignedIntegerAlias */
		$count = Arr::getOr($this->options->configuration, 'count', 0);
		Enforce::throwIf(0 <= $count); //@phpstan-ignore-line [DOCTYPE]
		$this->cleanup($count);
	}

	#region function

	private function toSafeFileNameHeader(): string
	{
		$trimHeader = Text::trim($this->options->header, '/\\');
		return Text::replace($trimHeader, ['/', '\\', '*', '|', '<', '>', '?', ':'], '_');
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
		$logFiles = Directory::find($this->directoryPath, $filePattern);
		$logCount = Arr::getCount($logFiles);
		if ($logCount <= $maxCount) {
			return;
		}

		$length = $logCount - $maxCount;
		for ($i = 0; $i < $length; $i++) {
			File::removeFile($logFiles[$i]);
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

		$filePattern = Text::replaceMap(
			$this->baseFileName,
			[
				'HEADER' => $this->toSafeFileNameHeader(),
				'DATE' => $this->toHeaderDate(true),
			]
		);
		if (!Arr::containsValue(self::$cleanupFilePatterns, $filePattern)) {
			self::$cleanupFilePatterns[] = $filePattern;
			$this->cleanupCore($maxCount, $filePattern);
		}
	}

	private function getLogFilePath(): string
	{
		$fileName = Text::replaceMap(
			$this->baseFileName,
			[
				'HEADER' => $this->toSafeFileNameHeader(),
				'DATE' => $this->toHeaderDate(false),
			]
		);

		return Path::combine($this->directoryPath, $fileName);
	}

	#endregion

	#region LoggerBase

	protected function logImpl(int $level, int $traceIndex, $message, ...$parameters): void
	{
		Directory::createDirectoryIfNotExists($this->directoryPath);

		$logMessage = $this->format($level, $traceIndex + 1, $message, ...$parameters);

		$filePath = $this->getLogFilePath();
		//error_logが制限されている場合はこっちを使用する→: file_put_contents($filePath, $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
		error_log($logMessage . PHP_EOL, 3, $filePath);
	}

	#endregion
}
