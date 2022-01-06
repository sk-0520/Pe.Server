<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

require_once(__DIR__ . '/../../Libs/smarty/libs/Smarty.class.php');

use \Smarty;
use \DOMElement;
use \DOMDocument;
use PeServer\Core\Csrf;
use PeServer\Core\I18n;
use PeServer\Core\Collection;
use \Smarty_Internal_Template;
use PeServer\Core\FileUtility;
use PeServer\Core\ArrayUtility;
use PeServer\Core\StringUtility;
use PeServer\Core\TypeConverter;
use PeServer\Core\InitializeChecker;
use PeServer\Core\Throws\CoreException;
use PeServer\Core\Mvc\TemplatePlugin\CsrfFunction;
use PeServer\Core\Mvc\TemplatePlugin\AssetFunction;
use PeServer\Core\Mvc\TemplatePlugin\InputHelperFunction;
use PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\Mvc\TemplatePlugin\ITemplateFunction;
use PeServer\Core\Mvc\TemplatePlugin\ShowErrorMessagesFunction;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;

/**
 * View側のテンプレート処理。
 *
 * 初期化の呼び出しが必須。
 */
abstract class Template
{
	/**
	 * 初期化チェック
	 *
	 * @var InitializeChecker|null
	 */
	protected static $initializeChecker;
	/**
	 * ルートディレクトリ。
	 *
	 * @var string
	 */
	protected static $rootDirectoryPath;

	/**
	 * ベースディレクトリ。
	 *
	 * @var string
	 */
	protected static $baseDirectoryPath;

	/**
	 * テンプレートディレクトリベース名。
	 *
	 * 内部で self::$baseDirectoryPath と引数をかけ合わせる。
	 *
	 * @var string
	 */
	private static string $templateBaseName;
	/**
	 * 一時ディレクトリベース名。
	 *
	 * 内部で self::$baseDirectoryPath と引数をかけ合わせる。
	 *
	 * @var string
	 */
	private static string $temporaryBaseName;

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $templateBaseName, string $temporaryBaseName): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$rootDirectoryPath = $rootDirectoryPath;
		self::$baseDirectoryPath = $baseDirectoryPath;
		self::$templateBaseName = $templateBaseName;
		self::$temporaryBaseName = $temporaryBaseName;
	}

	public static function create(string $baseName, string $templateBaseName = '', string $temporaryBaseName = ''): Template
	{
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		if (StringUtility::isNullOrWhiteSpace($templateBaseName)) {
			$templateBaseName = self::$templateBaseName;
		}
		if (StringUtility::isNullOrWhiteSpace($temporaryBaseName)) {
			$temporaryBaseName = self::$temporaryBaseName;
		}

		return new _Template_Impl($baseName, $templateBaseName, $temporaryBaseName);
	}

	/**
	 * View描画処理。
	 *
	 * @param string $templateName テンプレート名。
	 * @param TemplateParameter $parameter パラメータ。
	 * @return void no-return?
	 */
	public abstract function show(string $templateName, TemplateParameter $parameter): void;
	public abstract function build(string $templateName, TemplateParameter $parameter): string;
}

class _Template_Impl extends Template
{
	/**
	 * Undocumented variable
	 *
	 * @var Smarty
	 */
	private $engine;

	public function __construct(string $baseName, string $templateBaseName, string $temporaryBaseName)
	{
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		$this->engine = new Smarty();
		$this->engine->addTemplateDir(FileUtility::joinPath(parent::$baseDirectoryPath, $templateBaseName, $baseName));
		$this->engine->addTemplateDir(FileUtility::joinPath(parent::$baseDirectoryPath, $templateBaseName));
		$this->engine->setCompileDir(FileUtility::joinPath(parent::$baseDirectoryPath, $temporaryBaseName, 'compile', $baseName));
		$this->engine->setCacheDir(FileUtility::joinPath(parent::$baseDirectoryPath, $temporaryBaseName, 'cache', $baseName));
		$this->engine->escape_html = true;

		$this->registerPlugins();
	}

	private function applyParameter(TemplateParameter $parameter): void
	{
		// @phpstan-ignore-next-line
		$this->engine->assign([
			'status' => $parameter->httpStatus,
			'values' => $parameter->values,
			'errors' => $parameter->errors,
		]);
	}

	public function show(string $templateName, TemplateParameter $parameter): void
	{
		$this->applyParameter($parameter);
		// @phpstan-ignore-next-line
		$this->engine->display($templateName);
	}

	public function build(string $templateName, TemplateParameter $parameter): string
	{
		$this->applyParameter($parameter);
		// @phpstan-ignore-next-line
		return $this->engine->fetch($templateName);
	}

	private function registerPlugins(): void
	{
		$argument = new TemplatePluginArgument(
			$this->engine,
			self::$rootDirectoryPath,
			self::$baseDirectoryPath
		);
		$showErrorMessagesFunction = new ShowErrorMessagesFunction($argument);
		/** @var array<ITemplateFunction> */
		$plugins = [
			new CsrfFunction($argument),
			new AssetFunction($argument),
			$showErrorMessagesFunction,
			new InputHelperFunction($argument, $showErrorMessagesFunction),
		];
		foreach ($plugins as $plugin) {
			if ($plugin instanceof ITemplateFunction) {
				// @phpstan-ignore-next-line
				$this->engine->registerPlugin('function', $plugin->getFunctionName(), array($plugin, 'functionBody'));
			}
		}
	}
}
