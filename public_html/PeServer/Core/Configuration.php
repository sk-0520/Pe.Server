<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;

class Configuration
{
	public const FILE_TYPE_DEFAULT = '';
	public const FILE_TYPE_JSON = 'json';

	private string $environment;

	public function __construct(string $environment)
	{
		$this->environment = $environment;
	}

	protected function getEnvironmentFileName(string $fileName): string
	{
		$baseFileName = FileUtility::getFileNameWithoutExtension($fileName);
		$baseFileExtension = FileUtility::getFileExtension($fileName, false);
		$environmentFileName = $baseFileName . '.' . $this->environment . '.' . $baseFileExtension;

		return $environmentFileName;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $directoryPath
	 * @param string $fileName
	 * @param string $fileType ファイル種別。未指定(FILE_TYPE_DEFAULT)の場合はファイル拡張子から判断する
	 * @return array<mixed>
	 */
	public function load(string $directoryPath, string $fileName, string $fileType = self::FILE_TYPE_DEFAULT): array
	{
		$baseFileExtension = FileUtility::getFileExtension($fileName, false);

		$confType = $fileType;
		if ($fileType === self::FILE_TYPE_DEFAULT) {
			$confType = match (StringUtility::toLower($baseFileExtension)) {
				'json' => self::FILE_TYPE_JSON,
				default => self::FILE_TYPE_DEFAULT,
			};
		}
		// とりあえずは JSON だけ相手にする
		if ($confType !== self::FILE_TYPE_JSON) {
			throw new ArgumentException('$fileName');
		}

		$baseFilePath = FileUtility::joinPath($directoryPath, $fileName);
		$environmentFilePath = FileUtility::joinPath($directoryPath, $this->getEnvironmentFileName($fileName));

		/** @var array<mixed> */
		$configuration = array();

		/** @var array<mixed> */
		$baseConfiguration = FileUtility::readJsonFile($baseFilePath);
		if (file_exists($environmentFilePath)) {
			/** @var array<mixed> */
			$environmentConfiguration = FileUtility::readJsonFile($environmentFilePath);
			$configuration = array_replace_recursive($baseConfiguration, $environmentConfiguration);
		} else {
			$configuration = $baseConfiguration;
		}

		return $configuration;
	}

	/**
	 * 設定データの再帰的置き換え。
	 *
	 * @param array<mixed> $array
	 * @param array<string,string> $map
	 * @param string $head
	 * @param string $tail
	 * @return array<mixed>
	 */
	public function replace(array $array, array $map, string $head, string $tail): array
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = $this->replace($value, $map, $head, $tail);
			} else if (is_string($value)) {
				$array[$key] = StringUtility::replaceMap($value, $map, $head, $tail);
			}
		}

		return $array;
	}
}
