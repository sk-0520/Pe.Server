<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

// require_once('PeServer/Libs/smarty/libs/Smarty.class.php');

use \Exception;
use PeServer\Core\ArrayUtility;
use \Smarty;
use \Smarty_Internal_Template;
use \PeServer\Core\InitializeChecker;

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

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $environment): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$rootDirectoryPath = $rootDirectoryPath;
		self::$baseDirectoryPath = $baseDirectoryPath;
	}

	public static function create(string $baseName): Template
	{
		self::$initializeChecker->throwIfNotInitialize();

		return new _Template_Invisible($baseName);
	}

	/**
	 * View描画処理。
	 *
	 * @param string $templateName テンプレート名。
	 * @param mixed $parameters パラメータ。
	 * @param array $options オプション。
	 * @return void no-return?
	 */
	public abstract function show(string $templateName, $parameters, array $options = array()): void; // @phpstan-ignore-line
}

class _Template_Invisible extends Template
{
	/**
	 * Undocumented variable
	 *
	 * @var Smarty
	 */
	private $engine;

	public function __construct(string $baseName)
	{
		self::$initializeChecker->throwIfNotInitialize();

		$this->engine = new Smarty();
		$this->engine->addTemplateDir(self::$baseDirectoryPath . "/App/Views/$baseName/");
		$this->engine->addTemplateDir(self::$baseDirectoryPath . "/App/Views/");
		$this->engine->setCompileDir(self::$baseDirectoryPath . "/data/temp/views/c/$baseName/");
		$this->engine->setCacheDir(self::$baseDirectoryPath . "/data/temp/views/t/$baseName/");

		$this->registerFunctions();
	}

	public function show(string $templateName, $parameters, array $options = array()): void // @phpstan-ignore-line
	{
		$this->engine->assign($parameters); // @phpstan-ignore-line
		$this->engine->display($templateName); // @phpstan-ignore-line
	}

	private function registerFunctions(): void
	{
		// @phpstan-ignore-next-line
		$this->engine->registerPlugin('function', 'show_error_messages', array($this, 'showErrorMessages'));
	}

	/**
	 * エラー表示
	 *
	 * @param array{string,string} $params
	 * @param Smarty_Internal_Template $smarty
	 * @return string HTML
	 */
	public function showErrorMessages(array $params, Smarty_Internal_Template $smarty): string
	{
		// @phpstan-ignore-next-line
		if (!isset($smarty->tpl_vars['errors'])) {
			return '';
		}

		/** @var array<string,string[]> */
		$errors = $smarty->tpl_vars['errors']->value;
		if (ArrayUtility::isNullOrEmpty($errors)) {
			return '';
		}

		return "<b>ERROR</b>";
	}
}
