<?php

declare(strict_types=1);

namespace PeServer\Core\Mvc\TemplatePlugin;

use \DOMElement;
use PeServer\Core\Environment;
use PeServer\Core\IO\IOUtility;
use PeServer\Core\IO\Path;
use PeServer\Core\IO\File;
use PeServer\Core\ArrayUtility;
use PeServer\Core\DefaultValue;
use PeServer\Core\Text;
use PeServer\Core\TypeUtility;
use PeServer\Core\Html\HtmlDocument;
use PeServer\Core\Throws\TemplateException;
use PeServer\Core\Mvc\TemplatePlugin\TemplateFunctionBase;
use PeServer\Core\Mvc\TemplatePlugin\TemplatePluginArgument;
use PeServer\Core\UrlUtility;

/**
 * 指定されたリソースをHTMLとして読み込む。
 *
 *  * 本番環境であればミニファイされたリソースを読もうとする
 *  * リビジョンをキャッシュバスターとして適用する
 *
 * $params
 *  * file: 対象リソース
 *  * auto_size: true/false trueの場合に実イメージサイズを読み込む(未指定は false)。
 *  * include: true/false trueの場合にファイルの中身を使用する(結構適当)(未指定は false)。
 *  * その他: 全部設定される
 */
class AssetFunction extends TemplateFunctionBase
{
	public function __construct(TemplatePluginArgument $argument)
	{
		parent::__construct($argument);
	}

	public function getFunctionName(): string
	{
		return 'asset';
	}

	/**
	 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
	 */
	protected function functionBodyImpl(): string
	{
		/** @var string */
		$sourcePath = ArrayUtility::getOr($this->params, 'file', DefaultValue::EMPTY_STRING);
		if (Text::isNullOrEmpty($sourcePath)) {
			return DefaultValue::EMPTY_STRING;
		}

		$isProduction = Environment::isProduction();

		$fileExtension = Path::getFileExtension($sourcePath);
		$extension = Text::toLower($fileExtension);

		$ignoreAsset = UrlUtility::isIgnoreCaching($sourcePath);

		$resourcePath = $sourcePath;
		if (!$ignoreAsset) {
			if ($isProduction) {
				$dir = Path::getDirectoryPath($sourcePath);
				$file = Path::getFileNameWithoutExtension($sourcePath);

				$resourcePath = $dir . '/' . $file . '.min.' . $fileExtension;
			}

			$resourcePath .= '?' . Environment::getRevision();
		}

		$dom = new HtmlDocument();
		if (!$isProduction) {
			$dom->addComment(Text::dump($this->params));
		}

		$autoSize = TypeUtility::parseBoolean(ArrayUtility::getOr($this->params, 'auto_size', 'false'));
		$include = TypeUtility::parseBoolean(ArrayUtility::getOr($this->params, 'include', 'false'));

		$filePath = Path::combine($this->argument->rootDirectoryPath, $sourcePath);
		if (($autoSize || $include) || !FIle::exists($filePath)) {
			// @phpstan-ignore-next-line nullは全取得だからOK
			foreach ($this->argument->engine->getTemplateDir(null) as $dir) {
				$path = Path::combine($dir, $sourcePath);
				if (IOUtility::existsItem($path)) {
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
					$element = $dom->addElement('style');

					$content = File::readContent($filePath);
					$element->addText($content->toString());
				} else {
					$element = $dom->addElement('link');

					$element->setAttribute('rel', 'stylesheet');
					$element->setAttribute('href', $resourcePath);
					$skipAttributes = array_merge($skipAttributes, ['rel', 'href']);
				}
				break;

			case 'js':
				$element = $dom->addElement('script');

				if ($include) {
					$content = File::readContent($filePath);
					$element->addText($content->toString());
				} else {
					$element->setAttribute('src', $resourcePath);
					$skipAttributes = array_merge($skipAttributes, ['src']);
				}
				break;

			case 'png':
			case 'jpeg':
			case 'jpg':
				$element = $dom->addElement('img');

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
				throw new TemplateException($resourcePath);
		}

		foreach ($this->params as $key => $value) {
			if (ArrayUtility::containsValue($skipAttributes, $key)) {
				continue;
			}
			$element->setAttribute($key, $value);
		}

		return $dom->build();
	}
}
