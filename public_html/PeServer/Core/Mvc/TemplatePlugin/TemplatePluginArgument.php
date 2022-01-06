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

	public function __construct(Smarty $engine, string $rootDirectoryPath, string $baseDirectoryPath)
	{
		$this->engine = $engine;
		$this->rootDirectoryPath = $rootDirectoryPath;
		$this->baseDirectoryPath = $baseDirectoryPath;
	}
}
