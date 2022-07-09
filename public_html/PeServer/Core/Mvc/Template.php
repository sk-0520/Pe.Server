<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

require_once(__DIR__ . '/../../Core/Libs/smarty/libs/Smarty.class.php');

use \Smarty;
use PeServer\Core\PathUtility;
use PeServer\Core\Store\Stores;
use PeServer\Core\StringUtility;
use PeServer\Core\InitializeChecker;
use PeServer\Core\Store\CookieStore;
use PeServer\Core\Store\SessionStore;
use PeServer\Core\Store\SpecialStore;
use PeServer\Core\Store\TemporaryStore;
use PeServer\Core\Mvc\TemplateParameter;
use PeServer\Core\Throws\NotImplementedException;
use PeServer\Core\Mvc\TemplatePlugin\CsrfFunction;
use PeServer\Core\Mvc\TemplatePlugin\AssetFunction;
use PeServer\Core\Mvc\TemplatePlugin\MarkdownFunction;
use PeServer\Core\Mvc\TemplatePlugin\ITemplateFunction;
use PeServer\Core\Mvc\TemplatePlugin\InputHelperFunction;
use PeServer\Core\Mvc\TemplatePlugin\BotTextImageFunction;
use PeServer\Core\Mvc\TemplatePlugin\ITemplateBlockFunction;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;
use PeServer\Core\Mvc\TemplatePlugin\ShowErrorMessagesFunction;

/**
 * View側のテンプレート処理。
 *
 * 初期化の呼び出しが必須。
 */
abstract class Template
{
	/**
	 * 初期化チェック
	 */
	protected static InitializeChecker $initializeChecker;
	/**
	 * ルートディレクトリ。
	 */
	protected static string $rootDirectoryPath;

	/**
	 * ベースディレクトリ。
	 */
	protected static string $baseDirectoryPath;

	/**
	 * テンプレートディレクトリベース名。
	 *
	 * 内部で self::$baseDirectoryPath と引数をかけ合わせる。
	 */
	private static string $templateBaseName;
	/**
	 * 一時ディレクトリベース名。
	 *
	 * 内部で self::$baseDirectoryPath と引数をかけ合わせる。
	 */
	private static string $temporaryBaseName;

	protected static SpecialStore $specialStore;
	protected static CookieStore $cookieStore;
	protected static SessionStore $sessionStore;
	protected static TemporaryStore $temporaryStore;

	public static function initialize(Stores $stores, string $rootDirectoryPath, string $baseDirectoryPath, string $templateBaseName, string $temporaryBaseName): void
	{
		self::$initializeChecker ??= new InitializeChecker();
		self::$initializeChecker->initialize();

		self::$specialStore = $stores->special;
		self::$cookieStore = $stores->cookie;
		self::$sessionStore = $stores->session;
		self::$temporaryStore = $stores->temporary;

		self::$rootDirectoryPath = $rootDirectoryPath;
		self::$baseDirectoryPath = $baseDirectoryPath;

		self::$templateBaseName = $templateBaseName;
		self::$temporaryBaseName = $temporaryBaseName;
	}

	public static function create(string $baseName, string $templateBaseName, string $temporaryBaseName): Template
	{
		self::$initializeChecker->throwIfNotInitialize();

		if (StringUtility::isNullOrWhiteSpace($templateBaseName)) {
			$templateBaseName = self::$templateBaseName;
		}
		if (StringUtility::isNullOrWhiteSpace($temporaryBaseName)) {
			$temporaryBaseName = self::$temporaryBaseName;
		}

		return new LocalSmartyTemplateImpl($baseName, $templateBaseName, $temporaryBaseName, self::$specialStore, self::$cookieStore, self::$sessionStore, self::$temporaryStore);
	}

	/**
	 * View描画処理。
	 *
	 * @param string $templateName テンプレート名。
	 * @param TemplateParameter $parameter パラメータ。
	 * @return string
	 */
	public abstract function build(string $templateName, TemplateParameter $parameter): string;
}

class LocalSmartyTemplateImpl extends Template
{
	/**
	 * テンプレートエンジン。
	 */
	private Smarty $engine;

	private SpecialStore $special;
	private CookieStore $cookie;
	private SessionStore $session;
	private TemporaryStore $temporary;

	public function __construct(string $baseName, string $templateBaseName, string $temporaryBaseName, SpecialStore $special, CookieStore $cookie, SessionStore $session, TemporaryStore $temporary)
	{
		parent::$initializeChecker->throwIfNotInitialize();

		$this->special = $special;
		$this->cookie = $cookie;
		$this->session = $session;
		$this->temporary = $temporary;

		$this->engine = new Smarty();
		$this->engine->addTemplateDir(PathUtility::joinPath(parent::$baseDirectoryPath, $templateBaseName, $baseName));
		$this->engine->addTemplateDir(PathUtility::joinPath(parent::$baseDirectoryPath, $templateBaseName));
		$this->engine->setCompileDir(PathUtility::joinPath(parent::$baseDirectoryPath, $temporaryBaseName, 'compile', $baseName));
		$this->engine->setCacheDir(PathUtility::joinPath(parent::$baseDirectoryPath, $temporaryBaseName, 'cache', $baseName));
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
			'stores' => [
				'special' => $this->special,
				'cookie' => $this->cookie,
				'session' => $this->session,
				'temporary' => $this->temporary,
			],
		]);
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
			self::$baseDirectoryPath,
			$this->special,
			$this->cookie,
			$this->session,
			$this->temporary
		);
		$showErrorMessagesFunction = new ShowErrorMessagesFunction($argument);
		/** @var array<ITemplateFunction> */
		$plugins = [
			new CsrfFunction($argument),
			new AssetFunction($argument),
			$showErrorMessagesFunction,
			new InputHelperFunction($argument, $showErrorMessagesFunction),
			new BotTextImageFunction($argument),
			new MarkdownFunction($argument),
		];
		foreach ($plugins as $plugin) {
			if ($plugin instanceof ITemplateBlockFunction) {
				// @phpstan-ignore-next-line
				$this->engine->registerPlugin('block', $plugin->getFunctionName(), array($plugin, 'functionBlockBody'));
			} else if ($plugin instanceof ITemplateFunction) { // @phpstan-ignore-line 増えたとき用にelseしたくないのである
				// @phpstan-ignore-next-line
				$this->engine->registerPlugin('function', $plugin->getFunctionName(), array($plugin, 'functionBody'));
			} else { //@phpstan-ignore-line
				throw new NotImplementedException();
			}
		}
	}
}
