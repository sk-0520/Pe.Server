<?php

declare(strict_types=1);

namespace PeServer\Core;

use PeServer\Core\Throws\ArgumentException;

class Configuration
{
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
	 * @return array<mixed>
	 */
	public function load(string $directoryPath, string $fileName): array
	{
		$baseFileExtension = FileUtility::getFileExtension($fileName, false);

		$baseFilePath = FileUtility::joinPath($directoryPath, $fileName);
		$environmentFilePath = FileUtility::joinPath($directoryPath, $this->getEnvironmentFileName($fileName));

		// とりあえずは JSON だけ相手にする
		if (StringUtility::toLower($baseFileExtension) !== 'json') {
			throw new ArgumentException('$fileName');
		}

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
	 * @param array{head:string,tail:string} $block
	 * @return array<mixed>
	 */
	public function replace(array $array, array $map, array $block = ['head' => '<|', 'tail' => '|>']): array
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = $this->replace($value, $map, $block);
			} else if (is_string($value)) {
				$array[$key] = StringUtility::replaceMap($value, $map, $block['head'], $block['tail']);
			}
		}

		return $array;
	}
}
