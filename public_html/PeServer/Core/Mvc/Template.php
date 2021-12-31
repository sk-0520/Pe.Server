<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc;

require_once(__DIR__ . '/../../Libs/smarty/libs/Smarty.class.php');

use \Smarty;
use \Smarty_Internal_Template;
use \DOMElement;
use \DOMDocument;
use \PeServer\Core\I18n;
use \PeServer\Core\Collection;
use \PeServer\Core\FileUtility;
use \PeServer\Core\ArrayUtility;
use PeServer\Core\Csrf;
use \PeServer\Core\StringUtility;
use \PeServer\Core\InitializeChecker;
use \PeServer\Core\Throws\CoreException;
use \PeServer\Core\Throws\InvalidOperationException;
use PeServer\Core\TypeConverter;

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

	public static function initialize(string $rootDirectoryPath, string $baseDirectoryPath, string $templateBaseName, string $temporaryBaseName, string $environment, string $revision): void
	{
		if (is_null(self::$initializeChecker)) {
			self::$initializeChecker = new InitializeChecker();
		}
		self::$initializeChecker->initialize();

		self::$rootDirectoryPath = $rootDirectoryPath;
		self::$baseDirectoryPath = $baseDirectoryPath;
		self::$templateBaseName = $templateBaseName;
		self::$temporaryBaseName = $temporaryBaseName;
		self::$environment = $environment;
		self::$revision = $revision;
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

		$this->registerFunctions();
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

	private function registerFunctions(): void
	{
		// @phpstan-ignore-next-line
		$this->engine->registerPlugin('function', 'show_error_messages', array($this, 'showErrorMessages'));
		// @phpstan-ignore-next-line
		$this->engine->registerPlugin('function', 'input_helper', array($this, 'inputHelper'));
		// @phpstan-ignore-next-line
		$this->engine->registerPlugin('function', 'csrf', array($this, 'embedCsrf'));
		// @phpstan-ignore-next-line
		$this->engine->registerPlugin('function', 'asset', array($this, 'asset'));
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

		$targetKey = Validator::COMMON;
		$classes = ['errors'];

		if (!isset($params['key']) || $params['key'] === Validator::COMMON) {
			$classes[] = 'common-error';
		} else {
			$classes[] = 'value-error';
			$targetKey = $params['key'];
		}

		if ($targetKey !== Validator::COMMON) {
			if (!isset($errors[$targetKey])) {
				return '';
			}
			if (ArrayUtility::isNullOrEmpty($errors[$targetKey])) {
				return '';
			}
		}

		$dom = new DOMDocument();

		$ulElement = $dom->createElement('ul');
		$ulElement->setAttribute('class', implode(' ', $classes));

		foreach ($errors as $key => $values) {
			if ($targetKey !== $key) {
				continue;
			}

			foreach ($values as $value) {
				$liElement = $dom->createElement('li');
				$liElement->setAttribute('class', 'error');

				$messageElement = $dom->createTextNode($value);

				$liElement->appendChild($messageElement);

				$ulElement->appendChild($liElement);
			}
		}

		if ($targetKey === Validator::COMMON) {
			$commonElement = $dom->createElement('div');
			$dom->appendChild($commonElement);

			$messageElement = $dom->createElement('p');
			$messageElement->appendChild($dom->createTextNode(I18n::message(I18n::COMMON_ERROR_TITLE)));
			$commonElement->appendChild($messageElement);
			if ($ulElement->childElementCount) {
				$commonElement->appendChild($ulElement);
			}

			$dom->appendChild($commonElement);
		} else {
			$dom->appendChild($ulElement);
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
		if (ArrayUtility::tryGet($params, 'auto_error', $autError)) {
			$showAutoError = filter_var($autError, FILTER_VALIDATE_BOOL) && boolval($autError);
		}

		$hasError = false;
		// @phpstan-ignore-next-line
		if (isset($smarty->tpl_vars['errors'])) {
			/** @var array<string,string[]> */
			$errors = $smarty->tpl_vars['errors']->value;
			foreach ($errors as $key => $values) {
				if ($targetKey === $key) {
					$hasError = 0 < ArrayUtility::getCount($values);
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
	 * CSRFトークン埋め込み処理。
	 *
	 * @param array<string,string> $params
	 * @param Smarty_Internal_Template $smarty
	 * @return string
	 */
	public function embedCsrf(array $params, Smarty_Internal_Template $smarty): string
	{
		// このタイミングではセッション処理完了を期待している

		if (!isset($_SESSION[Csrf::SESSION_KEY])) {
			return '';
		}

		$csrfToken = $_SESSION[Csrf::SESSION_KEY];

		$dom = new  DOMDocument();

		$element = $dom->createElement('input');
		$dom->appendChild($element);

		$element->setAttribute('type', 'hidden');
		$element->setAttribute('name', Csrf::REQUEST_KEY);
		$element->setAttribute('value', $csrfToken);

		return $dom->saveHTML(); // @phpstan-ignore-line
	}

	/**
	 * 指定されたリソースをHTMLとして読み込む。
	 *
	 *  * 本番環境であればミニファイされたリソースを読もうとする
	 *  * リビジョンをキャッシュバスターとして適用する
	 *
	 * @param array<string,string> $params
	 *  * file: 対象リソース
	 *  * auto_size: true/false trueの場合に実イメージサイズを読み込む
	 *  * include: true/false trueの場合にファイルの中身を使用する(結構適当)
	 *  * その他: 全部設定される
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

		$isProduction = parent::$environment === 'production';

		$extension = StringUtility::toLower(pathinfo($sourcePath, PATHINFO_EXTENSION));

		$ignoreAsset =
			StringUtility::startsWith($sourcePath, '//', false)
			||
			StringUtility::startsWith($sourcePath, 'https://', false)
			||
			StringUtility::startsWith($sourcePath, 'http://', false)
			||
			StringUtility::contains($sourcePath, '?', false);

		$resourcePath = $sourcePath;
		if (!$ignoreAsset) {
			if ($isProduction) {
				$dir = pathinfo($sourcePath, PATHINFO_DIRNAME);
				$file = pathinfo($sourcePath, PATHINFO_FILENAME);

				$resourcePath = $dir . '/' . $file . '.min.' . $extension;
			}

			$resourcePath .= '?' . parent::$revision;
		}

		$dom = new DOMDocument();
		if (!$isProduction) {
			$comment = $dom->createComment(StringUtility::dump($params));
			$dom->appendChild($comment);
		}

		$autoSize = TypeConverter::parseBoolean(ArrayUtility::getOr($params, 'auto_size', false));
		$include = TypeConverter::parseBoolean(ArrayUtility::getOr($params, 'include', false));

		$filePath = FileUtility::joinPath(parent::$rootDirectoryPath, $sourcePath);
		if(($autoSize || $include) || !is_file($filePath)) {
			 // @phpstan-ignore-next-line nullは全取得だからOK
			foreach($this->engine->getTemplateDir(null) as $dir) {
				$path = FileUtility::joinPath($dir, $sourcePath);
				if(is_file($path)) {
					$filePath = $path;
					break;
				}
			}
		}

		$skipAttributes = [
			'file',
			'auto_size',
			'include',
		];
		/** @var DOMElement */
		$element = null;

		switch ($extension) {
			case 'css':
				if ($include) {
					$element = $dom->createElement('style');
					$dom->appendChild($element);

					$content = file_get_contents($filePath);
					$element->appendChild($dom->createTextNode($content)); // @phpstan-ignore-line しんどい
				} else {
					$element = $dom->createElement('link');
					$dom->appendChild($element);

					$element->setAttribute('rel', 'stylesheet');
					$element->setAttribute('href', $resourcePath);
					$skipAttributes = array_merge($skipAttributes, ['rel', 'href']);
				}
				break;

			case 'js':
				$element = $dom->createElement('script');
				$dom->appendChild($element);

				if ($include) {
					$content = file_get_contents($filePath);
					$element->appendChild($dom->createTextNode($content)); // @phpstan-ignore-line しんどい
				} else {
					$element->setAttribute('src', $resourcePath);
					$skipAttributes = array_merge($skipAttributes, ['src']);
				}
				break;

			case 'png':
			case 'jpeg':
			case 'jpg':
				$element = $dom->createElement('img');
				$dom->appendChild($element);

				$element->setAttribute('src', $resourcePath);
				$skipAttributes = array_merge($skipAttributes, ['src']);

				if (!$ignoreAsset && ($autoSize || $include)) {
					$imageSize = getimagesize($filePath);
					if ($imageSize !== false) {
						$element->setAttribute('width', strval($imageSize[0]));
						$element->setAttribute('height', strval($imageSize[1]));
						$skipAttributes = array_merge($skipAttributes, ['width', 'height']);

						if ($include) {
							$content = file_get_contents($filePath);
							$base64 = base64_encode($content); // @phpstan-ignore-line しんどい
							$inline = 'data:' . $imageSize['mime'] . ';base64,' . $base64;
							$element->setAttribute('src', $inline);

						}
					}
				}
				break;

			default:
				throw new CoreException($resourcePath);
		}

		foreach ($params as $key => $value) {
			if (ArrayUtility::contains($skipAttributes, $key)) {
				continue;
			}
			$element->setAttribute($key, $value);
		}

		return $dom->saveHTML(); // @phpstan-ignore-line
	}
}
