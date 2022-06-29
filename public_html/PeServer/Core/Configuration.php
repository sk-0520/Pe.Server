<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\InitialValue;
use PeServer\Core\Throws\ArgumentException;

/**
 * 設定ファイルの読み込み加工処理。
 */
class Configuration
{
	public const FILE_TYPE_DEFAULT = InitialValue::EMPTY_STRING;
	public const FILE_TYPE_JSON = 'json';

	private string $environment;

	/**
	 * 生成
	 *
	 * @param string $environment 環境。
	 */
	public function __construct(string $environment)
	{
		$this->environment = $environment;
	}

	/**
	 * ファイル名に環境を付与。
	 *
	 * @param string $fileName 元となるファイル名
	 * @return string 環境が付与されたファイル名: name.env.ext
	 */
	protected function getEnvironmentFileName(string $fileName): string
	{
		$baseFileName = FileUtility::getFileNameWithoutExtension($fileName);
		$baseFileExtension = FileUtility::getFileExtension($fileName, false);
		$environmentFileName = $baseFileName . '.' . $this->environment . '.' . $baseFileExtension;

		return $environmentFileName;
	}

	/**
	 * 設定ファイル読み込み。
	 *
	 * @param string $directoryPath 設定ファイル配置ディレクトリ。
	 * @param string $fileName ファイル名。
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
	 * @param array<mixed> $array 元データ。配列の値のみが置き換え対象となる。
	 * @param array<string,string> $map 置き換え設定
	 * @param string $head 置き換え開始文字列
	 * @param string $tail 置き換え終了文字列
	 * @return array<mixed>
	 */
	public function replace(array $array, array $map, string $head, string $tail): array
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = $this->replace($value, $map, $head, $tail);
			} else if (is_string($value)) {
				/** @phpstan-var literal-string $value */
				$array[$key] = StringUtility::replaceMap($value, $map, $head, $tail);
			}
		}

		return $array;
	}
}
