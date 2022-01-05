<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \Smarty;

class TemplatePluginArgument
{
	/**
	 * テンプレートエンジン。
	 */
	public Smarty $engine;

	/**
	 * ルートディレクトリ。
	 */
	public string $rootDirectoryPath;

	/**
	 * ベースディレクトリ。
	 */
	public string $baseDirectoryPath;

	/**
	 * 環境情報。
	 */
	public string $environment;

	/**
	 * キャッシュバスター用のあれ。
	 */
	public string $revision;

	public function __construct(Smarty $engine, string $rootDirectoryPath, string $baseDirectoryPath, string $environment, string $revision)
	{
		$this->engine = $engine;
		$this->rootDirectoryPath = $rootDirectoryPath;
		$this->baseDirectoryPath = $baseDirectoryPath;
		$this->environment = $environment;
		$this->revision = $revision;
	}
}