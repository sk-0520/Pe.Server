<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

// require_once('PeServer/Libs/smarty/libs/Smarty.class.php');

use \Exception;
use \DOMDocument;
use DOMElement;
use \Smarty;
use \Smarty_Internal_Template;
use \PeServer\Core\ArrayUtility;
use \PeServer\Core\Collection;
use \PeServer\Core\I18n;
use \PeServer\Core\InitializeChecker;
use \PeServer\Core\StringUtility;
use \PeServer\Core\Throws\CoreException;
use PeServer\Core\Throws\InvalidOperationException;

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
	 * Undocumented variable
	 *
	 * @var string
	 */
	protected static $environment;

	/**
	 * キャッシュバスター用のあれ。
	 *
	 * @var string
	 */
	protected static $revision;

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $environment, string $revision): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$rootDirectoryPath = $rootDirectoryPath;
		self::$baseDirectoryPath = $baseDirectoryPath;
		self::$environment = $environment;
		self::$revision = $revision;
	}

	public static function create(string $baseName): Template
	{
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		return new _Template_Invisible($baseName);
	}

	/**
	 * View描画処理。
	 *
	 * @param string $templateName テンプレート名。
	 * @param TemplateParameter $parameter パラメータ。
	 * @return void no-return?
	 */
	public abstract function show(string $templateName, TemplateParameter $parameter): void;
}

class _Template_Invisible extends Template
{
	/**
	 * Undocumented variable
	 *
	 * @var Smarty
	 */
	private $_engine;

	public function __construct(string $baseName)
	{
		self::$initializeChecker->throwIfNotInitialize(); // @phpstan-ignore-line null access

		$this->_engine = new Smarty();
		$this->_engine->addTemplateDir(self::$baseDirectoryPath . "/App/Views/$baseName/");
		$this->_engine->addTemplateDir(self::$baseDirectoryPath . "/App/Views/");
		$this->_engine->setCompileDir(self::$baseDirectoryPath . "/data/temp/views/c/$baseName/");
		$this->_engine->setCacheDir(self::$baseDirectoryPath . "/data/temp/views/t/$baseName/");
		$this->_engine->escape_html = true;

		$this->registerFunctions();
	}

	public function show(string $templateName, TemplateParameter $parameter): void
	{
		// @phpstan-ignore-next-line
		$this->_engine->assign([
			'status' => $parameter->httpStatus,
			'values' => $parameter->values,
			'errors' => $parameter->errors,
		]);
		// @phpstan-ignore-next-line
		$this->_engine->display($templateName);
	}

	private function registerFunctions(): void
	{
		// @phpstan-ignore-next-line
		$this->_engine->registerPlugin('function', 'show_error_messages', array($this, 'showErrorMessages'));
		// @phpstan-ignore-next-line
		$this->_engine->registerPlugin('function', 'input_helper', array($this, 'inputHelper'));
		// @phpstan-ignore-next-line
		$this->_engine->registerPlugin('function', 'asset', array($this, 'asset'));
	}

	/**
	 * エラー表示。
	 *
	 * @param array<string,string> $params
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

		$targetKey = Validations::COMMON;
		$classes = ['errors'];

		if (!isset($params['key']) || $params['key'] === Validations::COMMON) {
			$classes[] = 'common-error';
		} else {
			$classes[] = 'value-error';
			$targetKey = $params['key'];
		}

		$dom = new DOMDocument();

		$ulElement = $dom->createElement('ul');
		$ulElement->setAttribute('class', implode(' ', $classes));

		if ($targetKey === Validations::COMMON) {
			$commonElement = $dom->createElement('div');
			$dom->appendChild($commonElement);

			$messageElement = $dom->createElement('p');
			$messageElement->appendChild($dom->createTextNode(I18n::message('エラーあり')));
			$commonElement->appendChild($messageElement);
			$commonElement->appendChild($ulElement);

			$dom->appendChild($commonElement);
		} else {
			$dom->appendChild($ulElement);
		}

		foreach ($errors as $key => $values) {
			if ($targetKey !== $key) {
				continue;
			}

			$liElement = $dom->createElement('li');
			$liElement->setAttribute('class', 'error');

			foreach ($values as $value) {
				$messageElement = $dom->createTextNode($value);
				$liElement->appendChild($messageElement);
			}

			$ulElement->appendChild($liElement);
		}

		$result = $dom->saveHTML();
		if ($result === false) {
			throw new CoreException();
		}

		return $result;
	}

	/**
	 * 入力要素のヘルパー。
	 *
	 * @param array<string,string> $params
	 *  * key: 対象キー, valuesと紐づく
	 *  * type: 対象のinput[type="*"]かtextareaを指定。不明時は input としてそのまま生成される。radio/checkboxは想定していないのでなんか別の方法を考えた方がいい
	 *  * auto_error: true/false 未指定かtrueの場合にエラー表示も自動で行う(show_error_messages関数の内部呼び出し)
	 *
	 * @param Smarty_Internal_Template $smarty
	 * @return string HTML
	 */
	public function inputHelper(array $params, Smarty_Internal_Template $smarty): string
	{
		$targetKey = $params['key']; // 必須
		$showAutoError = true;
		if (isset($params['auto_error']) && $params['auto_error'] != 'true') {
			$showAutoError = false;
		}

		$hasError = false;
		// @phpstan-ignore-next-line
		if (isset($smarty->tpl_vars['errors'])) {
			/** @var array<string,string[]> */
			$errors = $smarty->tpl_vars['errors']->value;
			foreach ($errors as $key => $values) {
				if ($targetKey === $key) {
					$hasError = true;
					break;
				}
			}
		}

		$dom = new DOMDocument();
		/** @var DOMElement|false */
		$element = false;

		/** @var string,string|string[]|bool|int */
		$targetValue = '';
		/** @var array<string,string|string[]|bool|int> */
		// @phpstan-ignore-next-line
		if (isset($smarty->tpl_vars['values'])) {
			$values = $smarty->tpl_vars['values']->value;
			if (isset($values[$targetKey])) {
				$targetValue = $values[$targetKey];
			}
		}

		switch ($params['type']) {
			case 'textarea': {
					$element = $dom->createElement('textarea');

					$text = $dom->createTextNode($targetValue);
					$element->appendChild($text);
				}
				break;

			default: {
					$element = $dom->createElement('input');
					$element->setAttribute('type', $params['type']);
					$element->setAttribute('value', $targetValue);
				}
				break;
		}
		// @phpstan-ignore-next-line
		if (!$element) {
			throw new InvalidOperationException();
		}
		$dom->appendChild($element);

		$element->setAttribute('name', $targetKey);
		$ignoreKeys = ['key', 'type', 'value'];
		foreach ($params as $key => $value) {
			if (array_search($key, $ignoreKeys) !== false) {
				continue;
			}
			$element->setAttribute($key, $value);
		}
		if ($hasError) {
			$className = $element->getAttribute('class');
			if (StringUtility::isNullOrEmpty($className)) {
				$className = 'error';
			} else {
				$className .= ' error';
			}
			$element->setAttribute('class', $className);
		}

		if ($showAutoError) {
			return $dom->saveHTML() . $this->showErrorMessages(['key' => $targetKey], $smarty);
		}
		return $dom->saveHTML(); // @phpstan-ignore-line
	}

	/**
	 * 指定されたリソースをHTMLとして読み込む。
	 *
	 * @param array<string,string> $params
	 * @param Smarty_Internal_Template $smarty
	 * @return string
	 */
	public function asset(array $params, Smarty_Internal_Template $smarty): string
	{
		if (!isset($params['file'])) {
			return '';
		}

		$sourcePath = $params['file'];
		if (StringUtility::isNullOrEmpty($sourcePath)) {
			return '';
		}

		$extension = StringUtility::toLower(pathinfo($sourcePath, PATHINFO_EXTENSION));

		$ignoreAsset =
			StringUtility::startsWith($sourcePath, 'https://', false)
			||
			StringUtility::startsWith($sourcePath, 'http://', false);

		$resourcePath = $sourcePath;
		if (!$ignoreAsset) {
			if (self::$environment === 'production') {
				$dir = pathinfo($sourcePath, PATHINFO_DIRNAME);
				$file = pathinfo($sourcePath, PATHINFO_FILENAME);

				$resourcePath = $dir . '/' . $file . '.min.' . $extension;
			}

			$resourcePath .= '?' . self::$revision;
		}


		switch ($extension) {
			case 'css':
				return "<link href=\"$resourcePath\" rel=\"stylesheet\" />";

			case 'js':
				return "<script src=\"$resourcePath\"></script>";

			case 'png':
			case 'jpeg':
			case 'jpg':
				return "<img src=\"$resourcePath\" />";

			default:
				throw new CoreException($resourcePath);
		}
	}
}
