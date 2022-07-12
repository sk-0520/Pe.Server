<?php

declare(strict_types=1);

namespace PeServer\Core;

/**
 * オートローダー。
 *
 * NOTE: なにがあってもPHP標準関数ですべて処理すること。
 */
class AutoLoader
{
	/**
	 * 読み込みベースパス。
	 *
	 * @var string[]
	 * @phpstan-var non-empty-string[]
	 * @readonly
	 */
	private array $baseDirectoryPaths;

	/**
	 * 読み込み対象正規表現パターン。
	 *
	 * @var string
	 * @readonly
	 */
	private string $includePattern;

	/**
	 * 生成。
	 *
	 * @param string[] $baseDirectoryPaths ベースディレクトリ一覧。
	 * @phpstan-param non-empty-string[] $baseDirectoryPaths
	 * @param string $includePattern 読み込み対象パターン（正規表現）。
	 * @return void
	 */
	public function __construct(array $baseDirectoryPaths, string $includePattern)
	{
		$this->baseDirectoryPaths = $baseDirectoryPaths;
		$this->includePattern = $includePattern;
	}

	/**
	 * 登録。
	 *
	 * @return void
	 */
	public function register(): void
	{
		spl_autoload_register([$this, 'load']);
	}

	/**
	 * クラス読み込み。
	 *
	 * @param string $className
	 * @phpstan-param class-string $className
	 * @return void
	 */
	private function load(string $className): void
	{
		if (!preg_match($this->includePattern, $className)) {
			return;
		}

		foreach ($this->baseDirectoryPaths as $baseDirectoryPath) {
			$fileBasePath = str_replace('\\', DIRECTORY_SEPARATOR, $className);
			$filePath = $baseDirectoryPath . DIRECTORY_SEPARATOR . $fileBasePath . '.php';

			if (is_file($filePath)) {
				require $filePath;
			}
		}
	}
}
