<?php

declare(strict_types=1);

namespace PeServer\Core\Serialization;

use PeServer\Core\ArrayUtility;
use PeServer\Core\Code;
use PeServer\Core\DefaultValue;
use PeServer\Core\IO\Directory;
use PeServer\Core\IO\File;
use PeServer\Core\IO\Path;
use PeServer\Core\Text;
use PeServer\Core\Throws\ArgumentException;

/**
 * 設定ファイルの読み込み加工処理。
 */
class Configuration
{
	#region define

	public const FILE_TYPE_DEFAULT = DefaultValue::EMPTY_STRING;
	public const FILE_TYPE_JSON = 'json';

	#endregion

	#region variable

	/**
	 * 環境。
	 *
	 * @var string
	 * @readonly
	 */
	private string $environment;

	#endregion

	/**
	 * 生成
	 *
	 * @param string $environment 環境。
	 */
	public function __construct(string $environment)
	{
		$this->environment = $environment;
	}

	#region function

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
		$baseFileExtension = Path::getFileExtension($fileName, false);

		$confType = $fileType;
		if ($fileType === self::FILE_TYPE_DEFAULT) {
			$confType = match (Text::toLower($baseFileExtension)) {
				'json' => self::FILE_TYPE_JSON,
				default => self::FILE_TYPE_DEFAULT,
			};
		}
		// とりあえずは JSON だけ相手にする
		if ($confType !== self::FILE_TYPE_JSON) {
			throw new ArgumentException('$fileName');
		}

		$baseFilePath = Path::combine($directoryPath, $fileName);
		$environmentFileName = Path::setEnvironmentName($fileName, $this->environment);
		$environmentFilePath = Path::combine($directoryPath, $environmentFileName);

		/** @var array<mixed> */
		$configuration = [];

		/** @var array<mixed> */
		$baseConfiguration = File::readJsonFile($baseFilePath);
		if (File::exists($environmentFilePath)) {
			/** @var array<mixed> */
			$environmentConfiguration = File::readJsonFile($environmentFilePath);
			$configuration = ArrayUtility::replace($baseConfiguration, $environmentConfiguration);
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
	 * @phpstan-param non-empty-string $head
	 * @param string $tail 置き換え終了文字列
	 * @phpstan-param non-empty-string $tail
	 * @return array<mixed>
	 */
	public function replace(array $array, array $map, string $head, string $tail): array
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = $this->replace($value, $map, $head, $tail);
			} else if (is_string($value)) {
				$array[$key] = Text::replaceMap(Code::toLiteralString($value), $map, $head, $tail);
			}
		}

		return $array;
	}

	#endregion
}
